# Product Requirements Document (PRD): Private Couple Gallery Web App
**Edition: Home Server / Zero-Trust Tunnel (100% Free & Unlimited Storage)**

## 1. Main Concept
A beautiful private web app for couples to upload memories, photos, stories, journal posts, milestones, and shared moments together.

## 2. Product Vision
- A premium emotional memory space for two people.
- Feels modern, intimate, elegant, and personal.
- Like a mix of private social media + journal + gallery.
- **Privacy:** Absolute. Hosted locally, no third-party cloud storage, no data harvesting.

## 3. Tech Stack
- **Core:** Laravel latest stable version
- **Frontend:** Livewire latest version, Blade components, Tailwind CSS
- **Design System:** shadcn/ui inspired component styling for Laravel
- **Interactivity:** Alpine.js
- **Database:** MySQL or SQLite (SQLite highly recommended for easy local backups)
- **Queue:** Database Queue via `QUEUE_CONNECTION=database`
- **File Storage:** Local Storage (`storage/app/public`) for unlimited capacity using personal hard drive.
- **Networking:** Cloudflare Tunnels (Zero Trust) for secure HTTPS exposure.
- **Image Processing:** Intervention Image v3 (Server-side via PHP).

## 4. UI Requirements
- Premium modern look with shadcn/ui style design system.
- Clean cards, rounded corners, soft shadows, and elegant spacing.
- Responsive layout (mobile-first).
- **Typography:** No uppercase text, no tracking-wide, use sentence case labels.
- Romantic but minimal aesthetic, soft neutral palette.
- Subtle, polished animations.

## 5. Main Users
- User A & User B (Couple).

## 6. Relationship Model
- One shared private space for exactly two invited users.

## 7. Core Product Features
### 7.1 Authentication
- Register, login, forgot password, email verification.
- Remember me, secure sessions.
- Profile settings and avatar upload.

### 7.2 Invite Partner System
- User creates space -> generate invite link.
- Invite partner by email.
- Partner joins shared space (max 2 users).
- Pending invite status, revoke invite, leave relationship option.

### 7.3 Shared Dashboard
- Latest memories, upcoming anniversaries.
- Memory count, total photos, monthly activity.
- Relationship days counter, quick post button.

### 7.4 Timeline Feed
- Post types: photo post, gallery post, text story, milestone, anniversary, travel memory, video embed (optional), note post.
- Features: chronological order, pinned posts, heart reactions, comments between partners, edit/delete/archive post.

### 7.5 Gallery Module
- Single/multiple photo upload.
- Albums, tags, date taken, location (optional), captions.
- Favorite photos, slideshow mode, masonry grid, lightbox preview.

### 7.6 Shared Journal
- Long form writing (markdown optional), mood selector.
- Date-based entries, private draft, publish to shared feed, searchable memories.

### 7.7 Relationship Milestones
- Track: first meet, dating start, engagement/wedding, first trip, etc.
- Display: days/months/years together counter, countdown to anniversary.

### 7.8 Memory Map (Optional)
- Save memories by city, view memories by location, travel timeline.

### 7.9 Comments and Reactions
- Heart reaction, simple comment threads, notifications when partner comments.

### 7.10 Notifications
- In-app and optional email notifications for new posts, comments, anniversary reminders, and "on this day" memories.

### 7.11 Search
- Search across captions, journal text, dates, tags, and milestones.

### 7.12 Memory Calendar
- Posts by date, anniversaries, special days, and upload history.

### 7.13 This Day Memories
- Show memories from the same date in previous years.

### 7.14 Relationship Stats
- Days together, total posts/photos, most active month, countries visited, comment count.

### 7.15 Privacy and Security
- Private by default, no public pages.
- Cloudflare Tunnel secure routing.
- Secure signed invite links, session device management.

### 7.16 Settings
- Relationship settings: shared name, cover photo, anniversary date, theme preference.
- Account settings: profile photo, display name, password, notification settings.

### 7.17 Themes
- Soft light theme, elegant dark theme, warm pastel theme (optional).

### 7.18 Media Uploads & High-Quality Compression (Local Engine)
- **Support:** jpg, jpeg, png, heic (auto-converted), webp, mp4 (optional).
- **Media Handling (Anti-Blur Strategy):**
    - **Max resolution capping:** Resize images exceeding 2560px down to 2560px using Intervention Image.
    - **Format conversion:** Auto-convert all uploaded images to WebP format.
    - **Quality retention:** WebP compression quality at 85% (visually lossless).
    - **Thumbnails:** Generate standard thumbnails (800px) for gallery grid view.
    - **Storage:** Save processed files directly to Local Disk (`storage/app/public`).

### 7.19 Archive System
- Archive old posts, restore archived posts.

### 7.20 Export Memories
- Export posts as PDF memory book, export gallery ZIP, export relationship timeline.

### 7.21 Admin Internal Tools
- SQLite database backup tool, local storage monitoring, user support mode.

## 8. Database Models Suggested
- **Users:** id, name, email, password, avatar
- **Relationships:** id, created_by, name, cover_photo, anniversary_date, invite_code, status
- **Relationship Members:** id, relationship_id, user_id, role
- **Posts:** id, relationship_id, user_id, type, title, content, is_pinned, is_archived, published_at
- **Post Media:** id, post_id, original_file_name, file_path_original, file_path_thumbnail, file_type, file_size_kb, sort_order
- **Comments:** id, post_id, user_id, content
- **Reactions:** id, post_id, user_id, type
- **Milestones:** id, relationship_id, title, event_date, description
- **Notifications:** id, user_id, type, data, read_at
- **Tags:** id, name
- **Post Tags:** post_id, tag_id

## 9. Performance & Processing Logic
- Paginate feed and lazy load images using 800px thumbnails.
- Cache dashboard counters.
- **ASYNC PROCESSING:** Upload raw file to local temp -> Dispatch Database Queue Job -> Compress using Intervention Image v3 (Generate 2560px & 800px) -> Save to `storage/app/public` -> Delete local temp.

## 10. SEO & Testing
- **SEO:** Landing page optimized, meta tags, social preview image.
- **Testing:** Feature tests for auth, invite flow, post CRUD, upload, and permissions.
