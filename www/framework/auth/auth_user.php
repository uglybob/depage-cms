<?php 
/**
 * @file    auth_user.php
 *
 *
 * copyright (c) 2002-2010 Frank Hellenkamp [jonas@depagecms.net]
 *
 * @author    Frank Hellenkamp [jonas@depagecms.net]
 */

/**
 * contains functions for handling user authentication
 * and session handling.
 */
class auth_user {
    // {{{ constructor()
    /**
     * constructor
     *
     * @public
     *
     * @param       PDO     $pdo        pdo object for database access
     *
     * @return      void
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    // }}}
    // {{{ get_by_username()
    /**
     * gets a user-object by username directly from database
     *
     * @public
     *
     * @param       PDO     $pdo        pdo object for database access
     * @param       string  $username   username of the user
     *
     * @return      auth_user
     */
    static public function get_by_username($pdo, $username) {
        $uid_query = $pdo->prepare(
            "SELECT 
                user.id AS id,
                user.name as name,
                user.name_full as fullname,
                user.pass as passwordhash,
                user.email as email,
                user.settings as settings,
                user.level as level
            FROM
                {$pdo->prefix}_auth_user AS user
            WHERE
                name = :name"
        );
        $uid_query->execute(array(
            ':name' => $username,
        ));
        $user = $uid_query->fetchObject("auth_user", array($pdo));

        return $user;
    }
    // }}}
    // {{{ get_by_sid()
    /**
     * gets a user-object by sid (session-id) directly from database
     *
     * @public
     *
     * @param       PDO     $pdo        pdo object for database access
     * @param       string  $sid        session id
     *
     * @return      auth_user
     */
    static public function get_by_sid($pdo, $sid) {
        $uid_query = $pdo->prepare(
            "SELECT 
                user.id AS id,
                user.name as name,
                user.name_full as fullname,
                user.pass as passwordhash,
                user.email as email,
                user.settings as settings,
                user.level as level
            FROM
                {$pdo->prefix}_auth_user AS user, 
                {$pdo->prefix}_auth_sessions AS sessions
            WHERE
                sessions.sid = :sid AND
                sessions.userid = user.id"
        );
        $uid_query->execute(array(
            ':sid' => $sid,
        ));
        $user = $uid_query->fetchObject("auth_user", array($pdo));

        return $user;
    }
    // }}}
    // {{{ get_useragent()
    /**
     * gets a user-object by sid (session-id) directly from database
     *
     * @public
     *
     * @param       PDO     $pdo        pdo object for database access
     * @param       string  $sid        session id
     *
     * @return      auth_user
     */
    public function get_useragent() {
        $cachepath = DEPAGE_CACHE_PATH . "browscap/";
        if (!is_dir($cachepath)) {
            mkdir($cachepath, 0777, true);
        }

        if (ini_get("browscap")) {
            $info = get_browser($this->useragent);
        } else {
            $browscap = new browscap($cachepath);
            $browscap->silent = true;
            $browscap->doAutoUpdate = false; // don't update now
            $browscap->lowercase = true; // don't update now
            //$browscap->updateMethod = Browscap::UPDATE_CURL;
            $info = $browscap->getBrowser($this->useragent);
        }

        return "{$info->browser} {$info->version} on {$info->platform}";
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker : */
