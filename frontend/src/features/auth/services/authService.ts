import { api } from "@/config/api/api";
import type { UserType } from "../type/userType";

interface RegisterPayload {
  name: string;
  username: string;
  email: string;
  password: string;
}

interface LoginPayload {
  email: string;
  password: string;
}

interface AuthResponse{
  token: string;
  user: UserType;
}

export async function register(data: RegisterPayload) {
  const response = await api.post("/register", data);
  return response.data;
}

export async function login(data: LoginPayload): Promise<AuthResponse> {
  const response = await api.post("/login", data);
  return response.data;
}

export async function logout() {
  const response = await api.post("/logout");
  return response.data;
}
