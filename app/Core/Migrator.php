<?php

namespace App\Core;

use PDO;
use Throwable;

/**
 * Applies pending migrations on the first request after a deployment.
 *
 * Code reaches the server by git; the database never did, so a deployment
 * that needed a schema or catalogue change sat half-applied until somebody
 * remembered to run bin/migrate.php. This closes that gap: the application
 * notices that the migration files have changed and brings the database up
 * with them, once, before serving the request.
 *
 * Three things keep that safe to do on a live site:
 *
 *   - It is free when there is nothing to do. The check is a stat of the
 *     migrations directory against a stored fingerprint, no query at all.
 *   - Only one request can be applying at a time, held by a named database
 *     lock. Anyone arriving mid-flight is served from the old state rather
 *     than queued behind it.
 *   - A failure is logged and swallowed. A migration that cannot apply must
 *     not take the site down with it; bin/migrate.php will report it properly.
 */
final class Migrator
{
    private const LOCK = 'tackrack_migrate';

    /** Where the fingerprint of the last applied set is kept. */
    private static function statePath(): string
    {
        return STORAGE_PATH . '/.migrations-state';
    }

    /**
     * A cheap signature of the migration files: their names and sizes, and
     * when they last changed. Any edit, addition or removal changes it.
     */
    private static function fingerprint(array $files): string
    {
        $parts = [];

        foreach ($files as $file) {
            $parts[] = basename($file) . ':' . filesize($file) . ':' . filemtime($file);
        }

        return sha1(implode('|', $parts));
    }

    /**
     * Bring the database up to the migrations on disk, if it is not already.
     * Never throws: a deployment problem should not become an outage.
     */
    public static function runPendingIfChanged(): void
    {
        try {
            $files = glob(BASE_PATH . '/database/migrations/*.sql') ?: [];

            if ($files === []) {
                return;
            }

            sort($files);

            $want  = self::fingerprint($files);
            $state = @file_get_contents(self::statePath());

            // The common case, on every request after the first: nothing has
            // changed since the last check, so there is nothing to do.
            if ($state !== false && trim($state) === $want) {
                return;
            }

            $pdo = Database::instance()->pdo();

            // One applier at a time. 0 means do not wait — another request is
            // already on it, and this one should just be served.
            if ((int) $pdo->query("SELECT GET_LOCK('" . self::LOCK . "', 0)")->fetchColumn() !== 1) {
                return;
            }

            try {
                self::apply($pdo, $files);
                @file_put_contents(self::statePath(), $want);
            } finally {
                $pdo->query("SELECT RELEASE_LOCK('" . self::LOCK . "')");
            }
        } catch (Throwable $e) {
            error_log('Migrator: ' . $e->getMessage());
        }
    }

    /**
     * Apply whatever has not been applied, in filename order, recording each
     * one so it is never applied twice. Stops at the first failure and leaves
     * the rest pending.
     */
    private static function apply(PDO $pdo, array $files): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS `migrations` (
                `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `filename`   VARCHAR(190) NOT NULL,
                `applied_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_migrations_filename` (`filename`)
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $applied = $pdo->query('SELECT `filename` FROM `migrations`')->fetchAll(PDO::FETCH_COLUMN);
        $record  = $pdo->prepare('INSERT IGNORE INTO `migrations` (`filename`) VALUES (?)');

        foreach ($files as $file) {
            $name = basename($file);

            if (in_array($name, $applied, true)) {
                continue;
            }

            try {
                $pdo->exec(file_get_contents($file));
                $record->execute([$name]);
                error_log('Migrator: applied ' . $name);
            } catch (Throwable $e) {
                // Leave it pending and stop, so a later one cannot be applied
                // on top of a database the earlier one failed to prepare.
                error_log('Migrator: FAILED ' . $name . ' — ' . $e->getMessage());
                throw $e;
            }
        }
    }
}
