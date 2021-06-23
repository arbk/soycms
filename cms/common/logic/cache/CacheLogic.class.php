<?php

class CacheLogic extends SOY2LogicBase
{
    //バージョンが異なる場合はtrue developingは無視
    public function checkCacheVersion()
    {
        if (SOYCMS_VERSION == "developing") {
            return false;
        }
        $res = false;
        if ($dh = opendir(SOY2HTMLConfig::CacheDir())) {
            while (($f = readdir($dh)) !== false) {
                if (strpos($f, ".") === 0 || is_dir($f)) {
                    continue;
                }
                $res = (strpos($f, SOYCMS_VERSION) === false);
                break;
            }
            closedir($dh);
        }
        return $res;
    }

    public function clearCache($jump = true)
    {
        $root = dirname(SOY2::RootDir());
        CMSUtil::unlinkAllIn($root . "/admin/cache/");
        CMSUtil::unlinkAllIn($root . "/soycms/cache/");
//      CMSUtil::unlinkAllIn($root . "/soyshop/cache/");
        CMSUtil::unlinkAllIn($root . "/app/cache/", true);

        // ファイルマネージャー（elfinder）のサムネイル画像
        CMSUtil::unlinkAllIn($root . "/soycms/tmb/", true);
//      CMSUtil::unlinkAllIn($root . "/soyshop/tmb/", true);

        $sites = SOY2Logic::createInstance("logic.admin.Site.SiteLogic")->getSiteList();
        foreach ($sites as $site) {
            CMSUtil::unlinkAllIn($site->getPath().SOYCMS_CACHE_DIRNAME."/", true);
        }

        if ($jump) {
          //リダイレクト
            SOY2PageController::jump("");
        }
    }
}
