<?php

namespace module\App\Controller;

class UserController extends \system\Template\AbstractController {

    public function autoCompleteAction() {

        //db.getCollection('Users').createIndex( { username: "text", first_name: "text" , last_name: "text" , email: "text" } )

        $searchtext = $this->getCode()->post('searchText');

        $qb = $this->getConnect()->createQueryBuilder(\module\Share\Model\Collection\User::class)
                ->text($searchtext)//full text search
                ->getQuery()
                ->execute();

        $users = [];
        if ($qb) {
            foreach ($qb as $val) {
                $users[] = $val->release();
            }
        }

        $this->getCode()->success("ok", ['users' => $users]);
    }

}
