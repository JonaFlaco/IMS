<?php

namespace App\Core;

class QueryFactory {

    private function getQueryFactory() {
        return new \Aura\SqlQuery\QueryFactory('sqlsrv');
    }

    public function newSelect(){

        $qf = $this->getQueryFactory();
        return $qf->newSelect();
    }

    public function newInsert(){

        $qf = $this->getQueryFactory();
        return $qf->newInsert();
    }

    public function newUpdate(){

        $qf = $this->getQueryFactory();
        return $qf->newUpdate();
    }

    public function newDelete(){

        $qf = $this->getQueryFactory();
        return $qf->newDelete();
    }


}