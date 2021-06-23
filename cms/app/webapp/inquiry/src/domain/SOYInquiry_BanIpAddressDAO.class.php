<?php
/**
 * @entity SOYInquiry_BanIpAddress
 */
abstract class SOYInquiry_BanIpAddressDAO extends SOY2DAO
{
    /**
     * @trigger onInsert
     */
    abstract public function insert(SOYInquiry_BanIpAddress $bean);

    /**
     * @return object
     */
    abstract public function getByIpAddress($ipAddress);

    abstract public function deleteByIpAddress($ipAddress);

    /**
     *  閲覧しているIPアドレスが禁止されているか調べて、期限切れの場合は禁止を解除する
     */
    public function checkBanByIpAddressAndUpdate($ipAddress)
    {
        try {
            $res = $this->executeQuery("SELECT log_date FROM soyinquiry_ban_ip_address WHERE ip_address = :attr", array(":attr" => $ipAddress));
        } catch (Exception $e) {
            return false;
        }

        if (!isset($res[0]["log_date"])) {
            return false;
        }

        //使用禁止したカートを再び使用可にする時間
        SOY2::import("domain.SOYInquiry_DataSetsDAO");
        $h = SOYInquiry_DataSets::get("form_ban_release", 3);
        if ($res[0]["log_date"] + $h * 60 * 60 < SOYCMS_NOW) {
            try {
                $this->executeUpdateQuery("DELETE FROM soyinquiry_ban_ip_address WHERE ip_address = :attr", array(":attr" => $ipAddress));
                return false;
            } catch (Exception $e) {
              //
            }
        }

        return true;
    }

    /**
     * @final
     */
    public function countInquiryCountByIpAddressWithinHour($ipAddress, $h = 1)
    {
        SOY2::import("domain.SOYInquiry_Inquiry");
        $sql = "SELECT COUNT(*) FROM soyinquiry_inquiry WHERE ip_address = :addr AND create_date >= :d AND flag != " . SOYInquiry_Inquiry::FLAG_DELETED;
        try {
            $res = $this->executeQuery($sql, array(":addr" => $ipAddress, ":d" => (SOYCMS_NOW - $h * 60 * 60)));
        } catch (Exception $e) {
            $res = array();
        }

        return (isset($res[0]["COUNT(*)"])) ? (int)$res[0]["COUNT(*)"] : 0;
    }

    /**
     * @final
     */
    public function onInsert($query, $binds)
    {
        $binds[":logDate"] = SOYCMS_NOW;
        return array($query, $binds);
    }
}
