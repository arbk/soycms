<?php

class CommentDetailAction extends SOY2Action
{
    private $commentId;

    public function execute()
    {
        $dao = SOY2DAOFactory::create("cms.EntryCommentDAO");

        try {
            $comment = $dao->getById($this->commentId);
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }

        $this->setAttribute("entity", $comment);

        return SOY2Action::SUCCESS;
    }

    public function getCommentId()
    {
        return $this->commentId;
    }
    public function setCommentId($commentId)
    {
        $this->commentId = $commentId;
    }
}
