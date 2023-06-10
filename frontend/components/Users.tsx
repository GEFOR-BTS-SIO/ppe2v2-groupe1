// component/User.tsx
import axios from "axios";
import { useState, useEffect } from "react";

export type User = {
  id: string;
  email: string;
};

type UserProps = {
  onUserSelected: (user: any) => void;
};

export const UserSelect = ({ onUserSelected }: UserProps) => {
  const [users, setUsers] = useState<User[]>([]);

  useEffect(() => {
    const fetchUsers = async () => {
      const response = await axios.post(
        `${process.env.NEXT_PUBLIC_API_URL}/apiuser`,
        {}
,        {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        }
      );
      const parsedResponse = JSON.parse(response.data); // Conversion de la réponse en objet JS
      setUsers(parsedResponse);
    };
    fetchUsers();
  }, []);

  const handleUserChange = (event: any) => {
    const selectedUserId = event.target.value; // Récupérer l'ID de l'utilisateur sélectionné
    onUserSelected(selectedUserId);
  };

  return (
    <select onChange={handleUserChange}>
      <option>Select user</option>
      {users.map((user) => (
        <option key={user.id} value={user.id}>
          {user.email}
        </option>
      ))}
    </select>
  );
};
