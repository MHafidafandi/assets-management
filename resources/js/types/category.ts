import type { PaginationLink } from './paginate';

export interface Category {
    id: number;
    name: string;
    is_active: boolean;
    assets_count: number;
}

export interface PaginatedCategory {
    data: Category[];
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
