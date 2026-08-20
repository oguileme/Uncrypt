import { api } from "@/config/api/api";

interface RegisterPayload {
  name: string;
  username: string;
  email: string;
  password: string;
}

export async function register(data: RegisterPayload) {
  const response = await api.post("/users", data);
  return response.data;
}
