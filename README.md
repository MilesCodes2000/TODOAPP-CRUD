# Advanced Todo App - PHP CRUD Application

A feature-rich, modern task management application built with PHP, MySQL, and Bootstrap 5.

## Features

### Core Functionality
- **CRUD Operations**: Create, Read, Update, and Delete tasks
- **Task Management**: Full task lifecycle management with intuitive interface
- **Responsive Design**: Mobile-first design that works on all devices

### Advanced Features

#### Priority System
- 4 priority levels: Low, Medium, High, Urgent
- Color-coded priority badges for quick identification
- Sort tasks by priority

#### Category/Tags
- Organize tasks with custom categories
- Category autocomplete based on existing categories
- Filter tasks by category
- Statistics breakdown by category

#### Search & Filter
- Full-text search across task titles and descriptions
- Filter by status (Pending/Completed)
- Filter by priority level
- Filter by category
- Sort by: Date Created, Due Date, Priority, Title
- Clear all filters with one click

#### Statistics Dashboard
- Real-time task statistics
- Total tasks, completed, pending, and overdue counts
- Priority distribution breakdown
- Category distribution breakdown
- Visual stat cards with hover effects

#### Bulk Operations
- Select multiple tasks with checkboxes
- Select all functionality
- Bulk actions: Mark as Complete, Mark as Pending, Archive, Delete
- Visual feedback for selected tasks
- Floating action panel

#### Task Notes/Comments
- Add notes to any task
- Timestamped notes
- View note history
- Notes preserved on task edit

#### Archive System
- Archive completed or old tasks
- Separate archived tasks view
- Keep main task list clean
- Archived tasks can be permanently deleted

#### Export Functionality
- Export tasks to CSV format
- Export tasks to JSON format
- Includes all task data and metadata
- Timestamped export files

#### Dark Mode
- Toggle between light and dark themes
- Persistent theme preference (localStorage)
- Smooth transitions
- Eye-friendly dark color scheme

#### Enhanced UI/UX
- Smooth animations and transitions
- Gradient backgrounds
- Card-based layout with depth
- Hover effects and micro-interactions
- Color-coded badges and status indicators
- Icon integration for better visual hierarchy
- Auto-dismissing alerts
- Loading states

#### Quick Actions
- One-click task completion
- Quick archive functionality
- Inline editing access
- Overdue task highlighting

## Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **CSS Framework**: Bootstrap 5.3
- **Icons**: Bootstrap Icons (SVG)

## Project Structure

```
project/
├── backend/
│   ├── config.php              # Database configuration
│   ├── crud.php                # Core CRUD functions + advanced features
│   ├── delete.php              # Delete task handler
│   ├── edit.php                # Edit task handler (AJAX)
│   ├── archive.php             # Archive task handler
│   ├── bulk_operations.php     # Bulk operations handler
│   ├── notes.php               # Task notes handler
│   ├── export.php              # Export functionality
│   └── quick_update.php        # Quick status update
├── frontend/
│   ├── index.php               # Main task list page
│   ├── edit.php                # Edit task page
│   ├── archived.php            # Archived tasks page
│   └── assets/
│       ├── css/
│       │   └── styles.css      # Custom styles + animations
│       └── js/
│           └── script.js       # JavaScript functionality
├── database/
│   └── migration.sql           # Database migration script
└── README.md
```

## Installation

1. **Database Setup**
   ```sql
   CREATE DATABASE todo_db;
   USE todo_db;

   -- Create main tasks table
   CREATE TABLE tasks (
       id INT AUTO_INCREMENT PRIMARY KEY,
       title VARCHAR(100) NOT NULL,
       description TEXT,
       due_date DATE,
       status ENUM('pending', 'completed') DEFAULT 'pending',
       priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
       category VARCHAR(50),
       archived TINYINT(1) DEFAULT 0,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   -- Run the migration script
   SOURCE database/migration.sql;
   ```

2. **Configure Database Connection**
   Edit `backend/config.php`:
   ```php
   $host = 'localhost';
   $dbname = 'todo_db';
   $username = 'your_username';
   $password = 'your_password';
   ```

3. **Deploy Files**
   - Place all files in your web server directory (e.g., `htdocs` for XAMPP)
   - Ensure PHP 7.4+ is installed
   - Ensure MySQL/MariaDB is running

4. **Access the Application**
   - Navigate to `http://localhost/project/frontend/index.php`

## Usage

### Adding a Task
1. Fill in the task title (required)
2. Add description, due date, priority, and category (optional)
3. Click "Add Task"

### Editing a Task
1. Click "Edit" on any task
2. Modify task details
3. Add notes in the sidebar
4. Click "Update Task"

### Filtering Tasks
1. Use the filter section above the task list
2. Enter search terms, select status, priority, or category
3. Choose sort order
4. Filters apply automatically

### Bulk Operations
1. Select multiple tasks using checkboxes
2. Use "Select All" for all tasks
3. Click desired action in the floating panel
4. Confirm action if required

### Archiving Tasks
1. Click "Archive" on individual tasks
2. Or use bulk archive for multiple tasks
3. View archived tasks from the Quick Stats panel

### Exporting Data
1. Click "Export CSV" or "Export JSON" in Quick Stats panel
2. File downloads automatically with timestamp

### Dark Mode
1. Click the theme toggle button (top-right)
2. Theme preference is saved automatically

## Database Schema

### tasks Table
- `id`: Primary key
- `title`: Task title (required, max 100 chars)
- `description`: Task description (optional)
- `due_date`: Due date (optional)
- `status`: pending or completed
- `priority`: low, medium, high, or urgent
- `category`: Task category (optional)
- `archived`: Archive flag (0 or 1)
- `created_at`: Timestamp

### task_notes Table
- `id`: Primary key
- `task_id`: Foreign key to tasks table
- `note_text`: Note content
- `created_at`: Timestamp

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Performance Features

- Database indexes on frequently queried columns
- Prepared statements for SQL injection prevention
- Lazy loading for archived tasks
- Optimized CSS animations
- Minimal JavaScript dependencies

## Security Features

- SQL injection protection via PDO prepared statements
- XSS prevention with htmlspecialchars()
- CSRF protection ready (can be added)
- Input validation and sanitization
- Secure file permissions recommended

## Future Enhancements

- User authentication and multi-user support
- Task sharing and collaboration
- Recurring tasks
- Task attachments
- Email notifications
- Calendar view
- Mobile apps
- API for third-party integrations

## License

Open source - feel free to use and modify for your projects.

## Credits

Built with modern PHP practices and Bootstrap 5 for a beautiful, functional task management experience.
