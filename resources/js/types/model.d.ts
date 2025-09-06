// resources/js/types/models.ts
export type Nullable<T> = T | null;

export interface Owner {
    id: number;
    first_name: string;
    last_name: string;
}

export interface AssetRow {
    id: number;
    reference: string;
    serial_number: string;
    description: Nullable<string>;
    current_owned_from: Nullable<string>;
    owner: Nullable<Owner>;
}

export interface PaginationLink {
    url: Nullable<string>;
    label: string;
    active: boolean;
}

export interface Pagination<T> {
    data: T[];
    links: PaginationLink[];
}
