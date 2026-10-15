# Database ERD — Real Props

Entity relationship diagram for the core domain. Spatie Permission (`roles`, `permissions`, `model_has_roles`, …), Cashier (`subscriptions`, `subscription_items`, customer columns on `users`), Media Library (`media`), Activity Log (`activities`), Notifications and framework tables (`cache`, `jobs`, `sessions`, `personal_access_tokens`) are omitted for readability.

```mermaid
erDiagram
    users ||--o| agent_profiles : "has profile"
    users ||--o{ properties : "lists (agent_id)"
    users ||--o{ posts : "authors (author_id)"
    users ||--o{ inquiries : "receives (agent_id)"
    users ||--o{ inquiries : "sends (user_id, optional)"
    users ||--o{ tour_requests : "receives (agent_id)"
    users ||--o{ tour_requests : "books (user_id, optional)"
    users ||--o{ favorites : "saves"
    users ||--o{ saved_searches : "creates"
    users ||--o{ subscriptions : "subscribes (Cashier)"

    agent_profiles }o--|| users : owner
    agent_profiles ||--o{ properties : "verified listings"

    properties }o--|| users : agent
    properties }o--|| property_types : type
    properties }o--o| cities : "city_id (nullable)"
    properties ||--o{ property_images : has
    properties ||--o{ inquiries : receives
    properties ||--o{ tour_requests : receives
    properties }o--o{ amenities : "many-to-many (amenity_property)"
    properties ||--o{ favorites : "is favorited"

    cities }o--|| states : located_in

    posts }o--o| categories : "category_id (nullable)"
    posts }o--o{ tags : "many-to-many (post_tag)"
    posts }o--|| users : author

    plans ||--o{ subscriptions : defines

    users {
        bigint id PK
        string name
        string email UK
        string password
        string phone "nullable"
        string whatsapp "nullable"
        text bio "nullable"
        string avatar_path "nullable"
        bool is_suspended
        timestamp suspended_at "nullable"
        string suspension_reason "nullable"
        timestamp email_verified_at "nullable"
    }

    agent_profiles {
        bigint id PK
        bigint user_id FK
        string agency_name "nullable"
        string license_number "nullable"
        string id_document_path "nullable"
        string license_document_path "nullable"
        string whatsapp "nullable"
        timestamp verified_at "nullable"
        string verified_by "nullable"
    }

    properties {
        bigint id PK
        bigint agent_id FK "users.id"
        bigint property_type_id FK
        bigint city_id FK "nullable"
        string title
        string slug UK
        longtext description
        decimal price 15_2
        string currency "default NGN"
        string listing_type "sale|rent"
        string status "available|pending|sold|rented"
        tinyint bedrooms
        tinyint bathrooms
        tinyint toilets
        int square_meters "nullable"
        smallint year_built "nullable"
        bool is_featured
        bool is_approved
        string rejected_reason "nullable"
        string featured_until "nullable"
        string address
        string city "denormalised"
        string state "denormalised"
        string country
        decimal latitude "nullable"
        decimal longitude "nullable"
        string video_url "nullable"
        string virtual_tour_url "nullable"
        string meta_title "nullable"
        string meta_description "nullable"
        string og_image "nullable"
        int views_count
        timestamp published_at "nullable"
        timestamp deleted_at "soft delete"
    }

    property_images {
        bigint id PK
        bigint property_id FK
        string url
        string alt "nullable"
        string caption "nullable"
        int sort_order
        bool is_cover
    }

    property_types {
        bigint id PK
        string name
        string slug UK
        string description "nullable"
        string icon "nullable"
        int sort_order
        bool is_active
    }

    amenities {
        bigint id PK
        string name
        string slug UK
        string icon "nullable"
        bool is_active
    }

    amenity_property {
        bigint property_id FK
        bigint amenity_id FK
    }

    states {
        bigint id PK
        string name
        string slug UK
        string code "nullable"
        string country
        bool is_active
    }

    cities {
        bigint id PK
        bigint state_id FK
        string name
        string slug UK
        decimal latitude "nullable"
        decimal longitude "nullable"
        bool is_active
    }

    inquiries {
        bigint id PK
        bigint property_id FK
        bigint agent_id FK "users.id"
        bigint user_id FK "nullable, authed sender"
        string name
        string email
        string phone "nullable"
        text message
        timestamp preferred_tour_date "nullable"
        string status "new|read|replied|archived"
        text reply_message "nullable"
        timestamp replied_at "nullable"
    }

    tour_requests {
        bigint id PK
        bigint property_id FK
        bigint user_id FK "booker"
        bigint agent_id FK "users.id"
        timestamp scheduled_at
        string status "requested|confirmed|cancelled|completed"
        text notes "nullable, from requester"
        text agent_notes "nullable"
    }

    favorites {
        bigint id PK
        bigint user_id FK
        bigint property_id FK
        unique "user_id+property_id"
    }

    saved_searches {
        bigint id PK
        bigint user_id FK
        string name
        json filters "criteria snapshot"
        bool notify
        timestamp last_notified_at "nullable"
        int matches_count
    }

    categories {
        bigint id PK
        string name
        string slug UK
        string description "nullable"
    }

    tags {
        bigint id PK
        string name
        string slug UK
    }

    post_tag {
        bigint post_id FK
        bigint tag_id FK
    }

    posts {
        bigint id PK
        bigint author_id FK "users.id"
        bigint category_id FK "nullable"
        string title
        string slug UK
        text excerpt "nullable"
        longtext content
        string status "draft|published|scheduled|archived"
        timestamp published_at "nullable"
        string meta_title "nullable"
        string meta_description "nullable"
        int views_count
        timestamp deleted_at "soft delete"
    }

    pages {
        bigint id PK
        string slug UK
        string title
        longtext content
        string meta_title "nullable"
        string meta_description "nullable"
        bool is_active
    }

    plans {
        bigint id PK
        string name
        string slug UK
        text description "nullable"
        decimal price 15_2
        string currency "3 letters"
        string billing_interval "month|year"
        string stripe_price_id "nullable"
        int listing_limit "0 = unlimited"
        bool allows_featured
        json features
        bool is_active
        int sort_order
    }

    subscriptions {
        bigint id PK
        string type "main|..."
        string stripe_id "nullable"
        string stripe_status "nullable"
        string stripe_price "nullable"
        int quantity "nullable"
        timestamp trial_ends_at "nullable"
        timestamp ends_at "nullable"
        bigint plan_id FK "nullable, app plan"
    }

    settings {
        bigint id PK
        string key UK
        text value "nullable"
        string group "general|seo|listings|social"
        string type "string|boolean"
    }

    contact_messages {
        bigint id PK
        string name
        string email
        string phone "nullable"
        string subject "nullable"
        text message
        string status "new|read|replied|closed|handled"
        text admin_note "nullable"
    }
```

## Notes

- **Denormalised location on `properties`**: `city`, `state` and `country` are stored as free-text alongside the optional `city_id` FK so search/filter stays fast even when master data changes or is incomplete.
- **Moderation**: `is_approved` + `rejected_reason` drive the admin approval workflow; `is_featured` / `featured_until` drive the paid/featured badge.
- **Verification**: `agent_profiles.verified_at` gates public agent profiles and public agent pages.
- **Subscriptions**: Cashier-owned `subscriptions` plus an app-level `plan_id` mapping to the `plans` table; `listing_limit = 0` means unlimited.
- **Settings**: key/value store grouped by `group` (`general`, `seo`, `listings`, `social`) managed from `/admin/settings`.
- **Slugs**: `posts`, `pages`, `properties`, `plans`, `categories`, `tags`, `amenities`, `property_types`, `states`, `cities` all carry unique slugs generated via the `HasSlug` concern.
