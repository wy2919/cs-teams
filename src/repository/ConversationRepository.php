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
        $records = $statement->fetchAll(PDO::FETCH_ASSOC);    // association array

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

    public function newConversation($user_1, $user_2): int {
        $statement = $this->database->connect()->prepare('
            SELECT c.id
            FROM public.conversations c
            WHERE c.id_user_1 = :id_1 AND c.id_user_2 = :id_2
            UNION
            SELECT c.id
            FROM public.conversations c
            WHERE c.id_user_1 = :id_2 AND c.id_user_2 = :id_1
        ');
        $statement->execute([$user_1, $user_2]);
        $conversationId = $statement->fetch(PDO::FETCH_ASSOC)['id'];

        if($conversationId == null) {
            $statement = $this->database->connect()->prepare('
                INSERT INTO public.conversations
                    (id_user_1, id_user_2) 
                    VALUES (?, ?) 
                    RETURNING id;
            ');
            $statement->execute([$user_1, $user_2]);

            $conversationId = $statement->fetch(PDO::FETCH_ASSOC)['id'];
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
}
