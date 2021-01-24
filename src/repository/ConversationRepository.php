<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/Conversation.php';
require_once __DIR__ . '/../mappers/ConversationMapper.php';
require_once __DIR__ . '/../mappers/MessageMapper.php';

class ConversationRepository extends Repository
{
    private ConversationMapper $conversationMapper;
    private MessageMapper $messageMapper;

    public function __construct()
    {
        parent::__construct();
        $this->conversationMapper = new ConversationMapper();
        $this->messageMapper = new MessageMapper();
    }

    public function getUserConversations(int $id)
    {
        session_start();
        $statement = $this->database->connect()->prepare('
            SELECT c.id,
                   c.id_user_2 AS id_user,
                   u.username,
                   u.image
            FROM public.conversations c
                LEFT JOIN public.users u ON c.id_user_2 = u.id
            WHERE c.id_user_1 = :id
            UNION
            SELECT c.id,
                   c.id_user_1 AS id_user,
                   u.username,
                   u.image
            FROM public.conversations c
                 LEFT JOIN public.users u ON c.id_user_1 = u.id
            WHERE c.id_user_2 = :id;
        ');
        $statement->execute([$id]);
        $records = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $this->conversationMapper->mapMultipleAssocToConversation($records);
    }

    public function getConversationMessages($conversationId)
    {
        $statement = $this->database->connect()->prepare('
            SELECT id_sender, message 
            FROM public.messages 
            WHERE id_conversation = :id
            ORDER BY created_at;
        ');
        $statement->execute([$conversationId]);
        $records = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $this->messageMapper->mapMultipleAssocToMessage($records);
    }

    public function getConversationMessagesAssoc($conversationId)
    {
        $messages =  $this->getConversationMessages($conversationId);

        $assocArr = array();
        foreach ($messages as $message){
            $assocObject['message'] = $message->getMessage();
            $assocObject['sendByFriend'] = $message->isSendByFriend();
            $assocArr[] = $assocObject;
        }
        return $assocArr;
    }

    public function createConversation($idUser1, $idUser2): int {

        $conversationId = $this->getConversationId($idUser1, $idUser2);

        if($conversationId == null) {
            $conversationId = $this->insertConversation($idUser1, $idUser2);
        }
        return $conversationId;
    }

    public function newMessage($conversationId, $senderId, $message): bool {
        $statement = $this->database->connect()->prepare('
            INSERT INTO public.messages
                (id_conversation, id_sender, message) 
                VALUES(?, ?, ?)
        ');
        return $statement->execute([$conversationId, $senderId, $message]);
    }

    private function insertConversation($idUser1, $idUser2) {
        $statement = $this->database->connect()->prepare('
                INSERT INTO public.conversations
                    (id_user_1, id_user_2) 
                    VALUES (?, ?) 
                    RETURNING id;
            ');
        $statement->execute([$idUser1, $idUser2]);

        $conversationId = $statement->fetch(PDO::FETCH_ASSOC)['id'];
        if(!$conversationId) {
            throw new UnexpectedValueException('Could not create conversation with specified users.');
        }

        return $conversationId;
    }

    private function getConversationId($idUser1, $idUser2) {
        $statement = $this->database->connect()->prepare('
            SELECT c.id
            FROM public.conversations c
            WHERE c.id_user_1 = :id_1 AND c.id_user_2 = :id_2
            UNION
            SELECT c.id
            FROM public.conversations c
            WHERE c.id_user_1 = :id_2 AND c.id_user_2 = :id_1
        ');
        $statement->execute([$idUser1, $idUser2]);
        return $statement->fetch(PDO::FETCH_ASSOC)['id'];
    }
}
