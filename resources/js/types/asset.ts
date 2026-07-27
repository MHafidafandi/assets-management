import type { Category } from './category';
import type { PaginationLink } from './paginate';

export interface Asset {
    id: number;
    category_id: number;
    name: string;
    thumbnail?: string;
    purchase_date: string;
    specification: Record<string, string> | null;
    is_active: boolean;
    category?: Pick<Category, 'id' | 'name'>;
    attachment?: {
        id: number;
        file_name: string;
        file_path: string;
    };
}

export interface PaginatedAssets {
    data: Asset[];
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
