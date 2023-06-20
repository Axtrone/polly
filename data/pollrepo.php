<?php
include_once('data/storage.php');
class PollStorage extends Storage {
    public function __construct() {
        parent::__construct(new JsonIO('data/polls.json'));
    }

    public function currentPolls(){
        $ret = array_filter($this->contents, fn($poll) => date("Y-m-d") <= $poll['deadline']);
        uasort($ret, array($this, 'cmp'));
        return $ret;
    }
    public function expiredPolls(){
        $ret = array_filter($this->contents, fn($poll) => date("Y-m-d") > $poll['deadline']);
        uasort($ret, array($this, 'cmp'));
        return $ret;
    }

    function cmp($a, $b){
        if(strcmp($a['createdAt'], $b['createdAt']) == 0) return 0;
        return (strcmp($a['createdAt'], $b['createdAt']) > 0) ? -1 : 1;
    }

    function voted($poll, $user){
        return in_array($user ,$poll['voted']);
    }
    
    function vote($poll, $user, $choices){
        $poll['voted'] []= $user;
        foreach ($choices as $ans) {
            $poll['answers'][$ans]++;
        }
        $this->update($poll['id'], $poll);
    }
}
?>