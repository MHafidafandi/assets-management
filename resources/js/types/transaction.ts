import type { PaginationLink } from './paginate';
import type { Asset } from './asset';
import type { UserDB } from './user';

export interface AssetTransaction {
    id: number;
    asset_id: number;
    user_id: number;
    borrowed_at: string;
    returned_at: string | null;
    is_returned: boolean;
    notes: Record<string, string> | null;
    asset?: Pick<Asset, 'id' | 'name'>;
    user?: Pick<UserDB, 'id' | 'name' | 'email'>;
}

export interface PaginatedTransactions {
    data: AssetTransaction[];
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
