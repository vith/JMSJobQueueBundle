<?php

namespace JMS\JobQueueBundle;

use Doctrine\DBAL\Exception\ConnectionException;

class DatabaseExceptionHandler
{
    // http://dev.mysql.com/doc/refman/5.5/en/error-messages-server.html#error_er_bad_db_error
    const MYSQL_ER_BAD_DB_ERROR = 1049;

    public static function handle(\Exception $e)
    {
        if (!self::exceptionIsIgnorable($e)) {
            throw $e;
        }

        printf("Ignoring %s in %s:\n\t%s\n", get_class($e), __METHOD__, $e->getMessage());
    }

    public static function exceptionIsIgnorable(\Exception $e)
    {
        if (!$e instanceof ConnectionException) {
            return false;
        }

        /* We must ignore ConnectionException when it's caused by the database not existing,
        otherwise it's impossible to create the database with
        `app/console doctrine:database:create` */

        if ($e->getErrorCode() !== self::MYSQL_ER_BAD_DB_ERROR) {
            // there are likely other cases that should also be excepted, especially
            // when using databases other than MYSQL
            return false;
        }

        return true;
    }
}
