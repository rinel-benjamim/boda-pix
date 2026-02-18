export interface Event {
  id: number;
  name: string;
  description?: string;
  cover_image?: string;
  event_date: string;
  access_code: string;
  is_private: boolean;
  is_admin: boolean;
  created_by: {
    id: number;
    name: string;
  };
  participants_count: number;
  media_count: number;
  created_at: string;
}

export interface Media {
  id: number;
  event_id: number;
  type: 'image' | 'video';
  size: number;
  url: string;
  thumbnail_url?: string;
  uploaded_by: {
    id: number;
    name: string;
  };
  created_at: string;
}

export interface PaginatedMedia {
  data: Media[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}
