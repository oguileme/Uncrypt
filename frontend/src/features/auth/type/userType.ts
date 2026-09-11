export interface UserType {
    id:number;
    name: string;
    username: string;
    email: string;
    password?: string;
    level: number;
    xp_progress: number;
    xp_levelup: number;
    is_admin?: boolean;
    created_at: string;
}




