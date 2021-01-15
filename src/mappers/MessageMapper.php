<?php


class MessageMapper
{
    public function mapAssocToMessage($record)
    {
        if ($record == false) {
            return null;
        }

        return new Message(
            $record['message'],
            $record['id_sender'] !== RouteGuard::getAuthenticatedUserId()
        );
    }

    public function mapMultipleAssocToMessage($records)
    {
        if ($records == false) {
            return null;
        }

        $messages = array();
        foreach ($records as $record) {
            $messages[] = $this->mapAssocToMessage($record);
        }
        return $messages;
    }
}