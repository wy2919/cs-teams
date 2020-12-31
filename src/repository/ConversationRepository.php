<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/Conversation.php';

class ConversationRepository extends Repository
{

    public function getUserConversations(int $id)
    {
        session_start();
        $conversationsSelect = $this->database->connect()->prepare('
            SELECT c.id,
                   c.id_user_2 as id_user,
                   u.username,
                   u.image
            FROM public.conversations c
            LEFT JOIN public.users u on c.id_user_2 = u.id
            WHERE c.id_user_1 = :id
            UNION
            SELECT c.id,
                   c.id_user_1 as id_user,
                   u.username,
                   u.image
            FROM public.conversations c
                     LEFT JOIN public.users u on c.id_user_1 = u.id
            WHERE c.id_user_2 = :id;
        ');
        $conversationsSelect->bindParam(':id', $id, PDO::PARAM_INT);
        $conversationsSelect->execute();

        $fetchedConversations = $conversationsSelect->fetchAll(PDO::FETCH_ASSOC);    // association array

        $conversations = array();
        foreach ($fetchedConversations as $conversation){
            $conversations[] = new Conversation(
                $conversation['id'],
                $conversation['id_user'],
                $conversation['username'],
                $conversation['image']
            );
        }
        return $conversations;
    }

    public function getConversationMessages($conversationId)
    {
        $conversationMessagesSelect = $this->database->connect()->prepare('
            SELECT id_sender, message from public.messages WHERE id_conversation = :id
            ORDER BY created_at;
        ');

        $conversationMessagesSelect->execute([$conversationId]);
        $fetchedMessaged = $conversationMessagesSelect->fetchAll(PDO::FETCH_ASSOC);

        $messages = array();
        foreach ($fetchedMessaged as $message) {
            $messages[] = new Message(
                $message['message'],
                $message['id_sender'] !== $_SESSION['id']
            );
        }
        return $messages;
    }

    public function newConversation($user_1, $user_2): int {
        $statement = $this->database->connect()->prepare('
            SELECT c.id
            FROM public.conversations c
            WHERE
                        c.id_user_1 = :id_1 AND c.id_user_2 = :id_2
            UNION
            SELECT c.id
            FROM public.conversations c
            WHERE
                        c.id_user_1 = :id_2 AND c.id_user_2 = :id_1
        ');
        $statement->execute([$user_1, $user_2]);
        $conversationId = $statement->fetch(PDO::FETCH_ASSOC)['id'];

        if($conversationId == null) {
            $statement = $this->database->connect()->prepare('
                INSERT INTO public.conversations(id_user_1, id_user_2) VALUES (?, ?) RETURNING id;
            ');
            $statement->execute([$user_1, $user_2]);

            $conversationId = $statement->fetch(PDO::FETCH_ASSOC)['id'];
        }

        return $conversationId;
    }

    public function newMessage($conversationId, $senderId, $message): bool {
        $statement = $this->database->connect()->prepare('
        INSERT INTO public.messages(id_conversation, id_sender, message) VALUES(?, ?, ?)
        ');
        return $statement->execute([$conversationId, $senderId, $message]);
    }
}
