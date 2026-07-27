export interface DataJob {
    id: number;
    status: 'queued' | 'processing' | 'completed' | 'failed';
    file_path?: string | null;
    error_message?: string | null;
}
