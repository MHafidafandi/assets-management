import type { PaginationLink } from './paginate';

export interface UserDB {
    id: number;
    name: string;
    email: string;
    role: string;
    is_active: boolean;
}

export interface PaginatedUsers {
    data: UserDB[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
    prev_page_url: string | null;
    next_page_url: string | null;
}
