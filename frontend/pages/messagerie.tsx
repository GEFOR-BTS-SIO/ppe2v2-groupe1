import { useForm } from "react-hook-form";
import { useEffect, useState } from "react";
import axios from "axios";
import { UserSelect } from "@/components/User";

type Message = {
  id: number;
  content: string;
  receiverId: number;
  senderId: number;
};

type MessagerieFormData = {
  content: string;
};

export default function Messagerie() {
  const { register, handleSubmit, reset } = useForm<MessagerieFormData>();
  const [selectedUserId, setSelectedUserId] = useState<number>();
  const [messagesList, setMessagesList] = useState<Message[]>([]);

  useEffect(() => {
    const fetchMessages = async () => {
      try {
        if (selectedUserId) {
          const response = await axios.post(
            `${process.env.NEXT_PUBLIC_API_URL}/apimessage`,
            { receiver_id: selectedUserId },
            {
              headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
              },
            }
          );
          const messages = response.data;
          setMessagesList(messages);
        }
      } catch (error) {
        console.log(error);
      }
    };

    fetchMessages();
  }, [selectedUserId]);

  const handleUserSelected = (userId: number) => {
    setSelectedUserId(userId);
  };

 const onSubmit = async (data: MessagerieFormData) => {
  try {
    const response = await axios.post(
      `${process.env.NEXT_PUBLIC_API_URL}/apimessage`,
      {
        content: data.content,
        receiver_id: selectedUserId,
      },
      {
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      }
    );

    const newMessage: Message = response.data;

    // Ajouter le nouveau message à l'état (state) de messages
    setMessagesList([...messagesList, newMessage]);

    reset();
  } catch (error) {
    console.log(error);
  }
};




  return (
    <div className="bg-blue-100 min-h-screen">
      <div className="max-w-3xl mx-auto px-4 py-8">
        <div className="bg-white rounded-lg shadow-md p-4">
          <div className="mb-4">
            {messagesList.map((message) => (
              <div
                key={message.id} // Ajoutez cette ligne pour spécifier la prop "key"
                className={`p-4 rounded-lg ${
                  message.receiverId === selectedUserId
                    ? "bg-orange-200 flex justify-start"
                    : "bg-blue-200 flex justify-end"
                }`}
              >
                <p
                  className={
                    message.receiverId === selectedUserId
                      ? "text-orange-900"
                      : "text-blue-900"
                  }
                >
                  {message.content}
                </p>
              </div>
            ))}
          </div>

          <form onSubmit={handleSubmit(onSubmit)}>
            <div className="flex items-center mb-4">
              <div className="mr-4">
                <UserSelect onUserSelected={handleUserSelected} />
              </div>
              <div className="flex-grow">
                <input
                  {...register('content')}
                  type="text"
                  className="border border-gray-300 rounded-lg py-2 px-4 w-full focus:outline-none focus:ring-2 focus:ring-blue-200"
                  placeholder="Message de test"
                />
              </div>
              <button
                type="submit"
                className="ml-4 bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-200"
              >
                Envoyer
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
