// resources/js/types/models.ts
export type Nullable<T> = T | null;

export interface Owner {
    id: number;
    first_name: string;
    last_name: string;
}

export interface Asset {
    id: number;
    reference: string;
    serial_number: string;
    description: Nullable<string>;
    current_owner_id: number | null;
    current_owned_from: Nullable<string>;
}

export interface AssetRow extends Asset {
    owner: Nullable<Owner>;
}

export interface PaginationLink {
    url: Nullable<string>;
    label: string;
    active: boolean;
}

export interface AssetCreateUpdatePayload extends Record<string, any> {
    reference: string;
    serial_number: string;
    description: string | null;
    owner_id: number | null;
    owned_from: string | null;
}

export interface AssetOwnerAssignmentRow {
    id: number;
    asset_id: number;
    owner_id: number;
    owned_from: string;
    owned_to: Nullable<string>;
    owner: Owner;
}

export interface EditAssetProps {
    asset: Asset;
    owners: Owner[];
    history: AssetOwnerAssignmentRow[];
}

export interface Pagination<T> {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    from: number;
    to: number;
    total: number;
    per_page: number;
    last_page: number;
}
