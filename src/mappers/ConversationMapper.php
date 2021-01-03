<?php

require_once __DIR__ . '/../models/Conversation.php';
class ConversationMapper
{
    public function mapAssocToConversation($record)
    {
        if ($record == false) {
            return null;
        }

        return new Conversation(
            $record['id'],
            $record['id_user'],
            $record['username'],
            $record['image']
        );
    }

    public function mapMultipleAssocToConversation($records)
    {
        if ($records == false) {
            return null;
        }

        $conversations = array();
        foreach ($records as $record) {
            $conversations[] = $this->mapAssocToConversation($record);
        }
        return $conversations;
    }
}