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
            $record['id_sender'] !== $_SESSION['id']
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