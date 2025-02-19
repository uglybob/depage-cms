<?php

namespace depage\cache; 

class cache_uncached extends cache {
    // {{{ exist
    /**
     * @brief return if a cache-item with $key exists
     *
     * @return      (bool) true if cache for $key exists, false if not
     */
    private function exist($key) {
        return false;
    }
    // }}}
    // {{{ age */
    /**
     * @brief returns age of cache-item with key $key
     *
     * @param       $key (string) key of cache item
     *
     * @return      (int) age as unix timestamp
     */
    public function age($key) {
        return false;
    }
    // }}}
    // {{{ setFile */
    /**
     * @brief saves cache data for key $key to a file
     *
     * @param   $key (string) key to save data in, may include namespaces divided by a forward slash '/'
     * @param   $data (string) data to save in file
     * @param   $saveGzippedContent (bool) if true, it saves a gzip file additional to plain string, defaults to false
     *
     * @return  (bool) true if saved successfully
     */
    public function setFile($key, $data, $saveGzippedContent = false) {
        return false;
    }
    // }}}
    // {{{ getFile */
    /**
     * @brief gets content of cache item by key $key from a file
     *
     * @param   $key (string) key of item to get
     *
     * @return  (string) content of cache item, false if the cache item does not exist
     */
    public function getFile($key) {
        return false;
    }
    // }}}
    // {{{ set */
    /**
     * @brief sets data ob a cache item
     *
     * @param   $key (string) key to save under
     * @param   $data (object) object to save. $data must be serializable
     *
     * @return  (bool) true on success, false on failure
     */
    public function set($key, $data) {
        return false;
    }
    // }}}
    // {{{ get */
    /**
     * @brief gets a cached object
     *
     * @param   $key (string) key of item to get
     *
     * @return  (object) unserialized content of cache item, false if the cache item does not exist
     */
    public function get($key) {
        return false;
    }
    // }}}
    // {{{ getUrl */
    /**
     * @brief returns cache-url of cache-item for direct access through http
     *
     * @param   $key (string) key of cache item
     *
     * @return  (string) url of cache-item
     */
    public function getUrl($key) {
        return false;
    }
    // }}}
    // {{{ delete */
    /**
     * @brief deletes a cache-item by key or by namespace
     *
     * If key ends on a slash, all items in this namespace will be deleted.
     *
     * @param   $key (string) key of item
     *
     * @return  void
     */
    public function delete($key) {
    }
    // }}}
}

/* vim:set ft=php fenc=UTF-8 sw=4 sts=4 fdm=marker et : */
