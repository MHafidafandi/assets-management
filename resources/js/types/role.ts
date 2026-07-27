import type { PaginationLink } from './paginate';

export interface Role {
    id: number;
    name: string;
    description?: string;
    created_at?: string;
}

export interface PaginatedRole {
    data: Role[];
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
