<?php

namespace system\Api;

class Member {

    /**
     * 
     * @param type $dm connect doctrine mongodb
     * @param type $member_ids
     */
    public static function getUsers($dm, $member_ids = array()) {
        $qb = $dm->createQueryBuilder(\module\Share\Model\Collection\Member::class)
                ->field("id")->in($member_ids)
                ->getQuery()
                ->execute();
        $arr = [];
        if ($qb) {
            foreach ($qb as $val) {
                $user = $val->getUser();
                if ($user) {
                    $obj = (object) [
                                "user_id" => $user->getId(),
                                "member_id" => $val->getId(),
                                "first_name" => $user->getFirstName(),
                                "last_name" => $user->getLastName(),
                                "name" => $user->getName(),
                                "image" => $user->getImage(),
                                "email" => $user->getEmail()
                    ];
                    
                    $arr [] = $obj;
                }
            }
        }
        
        return $arr;
    }

}
