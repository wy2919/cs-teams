<?php


class Message
{
    private string $message;
    private bool $sendByFriend;

    public function __construct(string $message, bool $sendByFriend)
    {
        $this->message = $message;
        $this->sendByFriend = $sendByFriend;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function isSendByFriend(): bool
    {
        return $this->sendByFriend;
    }

    public function setSendByFriend(bool $sendByFriend): void
    {
        $this->sendByFriend = $sendByFriend;
    }



}