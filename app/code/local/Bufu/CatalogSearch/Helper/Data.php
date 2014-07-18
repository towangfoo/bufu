<?php
/**
 * Bufu_CatalogSearch_Helper_Data
 */
class Bufu_CatalogSearch_Helper_Data extends Mage_CatalogSearch_Helper_Data
{
    const OPTION_EXCLUDEARTIST_NAME = 'a';
    const OPTION_EXCLUDETRACKS_NAME = 't';
    const OPTION_CHECKFOREXCLUDE_PARAMNAME = 'e';
    const OPTION_EXCLUDE_VALUE_TRUE = '1';
    const OPTION_SEARCHCATEGORY_NAME = 'cat';

    /**
     * @var array search categories
     *
     * @note: these numbers work on live site only!
     */
    private $_searchCategories = array(
        '0'  => 'All Categories',
        '4'  => 'Music',
        '7'  => 'DVD',
        '20' => 'Books',
        '5'  => 'Audiobooks'
    );

    /**
     * get name of category field
     *
     * @return string
     */
    public function getSearchCategoryFieldname()
    {
        return self::OPTION_SEARCHCATEGORY_NAME;
    }

    /**
     * return array of search categories
     *
     * @return array
     */
    public function getSearchCategories()
    {
        return $this->_searchCategories;
    }

    /**
     * get search category for this search
     *
     * @return array | false
     */
    public function getSelectedCategory()
    {
        if ($category = (string) $this->_getRequest()->getParam(self::OPTION_SEARCHCATEGORY_NAME)) {
            if (isset($this->_searchCategories[$category])) {
                return array(
                    'title' => $this->_searchCategories[$category],
                    'value' => $category
                );
            }
        }
        return false;
    }

    /**
     * Retrieve search option parameter name
     *
     * @return string
     */
    public function getExcludeArtistParamName()
    {
        return self::OPTION_EXCLUDEARTIST_NAME;
    }

    /**
     * Retrieve search option parameter name
     *
     * @return string
     */
    public function getExcludeTracksParamName()
    {
        return self::OPTION_EXCLUDETRACKS_NAME;
    }

    public function getCheckForExcludesParamName()
    {
        return self::OPTION_CHECKFOREXCLUDE_PARAMNAME;
    }

    /**
     * Retrieve search option parameter value
     *
     * @return string
     */
    public function getExcludeParamValueTrue()
    {
        return self::OPTION_EXCLUDE_VALUE_TRUE;
    }

    /**
     * determine if exclude artist is set
     *
     * @return boolean
     */
    public function isExcludeArtistTrue()
    {
        $checkExcludes = $this->_getRequest()->getParam($this->getCheckForExcludesParamName());
        $excludeArtist = $this->_getRequest()->getParam($this->getExcludeArtistParamName());
        if (!is_null($checkExcludes) && $excludeArtist !== self::OPTION_EXCLUDE_VALUE_TRUE) {
            return true;
        }
        return false;
    }

    /**
     * determine if exclude tracks is set
     *
     * @return boolean
     */
    public function isExcludeTracksTrue()
    {
        $checkExcludes = $this->_getRequest()->getParam($this->getCheckForExcludesParamName());
        $excludeTracks = $this->_getRequest()->getParam($this->getExcludeTracksParamName());
        if (!is_null($checkExcludes) && $excludeTracks !== self::OPTION_EXCLUDE_VALUE_TRUE) {
            return true;
        }
        return false;
    }
}