<?php

namespace App\Models;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Node;

class CTypeLog {

    private $ctypeId;
    private $contentId; 
    private $userId;
    private $justification;
    private $title;
    private $groupName;
    private $statusId;
    private $isComment;
    private $parentLogId;
    private $attachments;
    private $reasons;
    private $isPrivate;
    
    public function __construct($ctypeId) {
        
        $this->ctypeId = $ctypeId;
    }

    public function setContentId($value) {
        $this->contentId = $value;

        return $this;
    }

    public function setUserId(?int $value) {
        $this->userId = $value;

        return $this;
    }

    public function setReasons(?string $value) {
        $this->reasons = $value;

        return $this;
    }

    
    public function setJustification(?string $value) {
        $this->justification = $value;

        return $this;
    }

    public function setTitle(?string $value) {
        $this->title = $value;

        return $this;
    }

    public function setGroupNam(?string $value) {
        $this->groupName = $value;

        return $this;
    }

    public function setStatusId(?int $value) {
        $this->statusId = $value;

        return $this;
    }

    public function setIsComment(?bool $value) {
        $this->isComment = $value;

        return $this;
    }

    public function setParentLogId(?int $value) {
        $this->parentLogId = $value;

        return $this;
    }

    public function setAttachments(array $value) {
        $this->attachments = $value;

        return $this;
    }

    public function setIsPrivate(?bool $value){
        $this->isPrivate = $value;

        return $this;
    }

    public function save()
    {
        
        if (empty($this->userId) && Application::getInstance()->user->getId() !== null) {
            $this->setUserId(Application::getInstance()->user->getId());
        }

        $item = new Node("ctypes_logs");
        $item->user_id = $this->userId == 0 ? null : $this->userId;
        $item->title = $this->title;
        $item->justification = $this->justification;
        $item->content_id = $this->contentId;
        $item->is_comment = $this->isComment;
        $item->parent_log_id = $this->parentLogId == 0 ? null : $this->parentLogId;
        $item->ctype_id = $this->ctypeId;
        $item->group_name = $this->groupName;
        $item->status_id = $this->statusId == 0 ? null : $this->statusId;
        $item->attachments = $this->attachments;
        $item->reasons = $this->reasons == null ? null : array_diff(explode(",", $this->reasons), [0]);
        $item->is_private = $this->isPrivate;

        $results = $item->save(["dont_add_log" => true]);

        return $results;
    }
}