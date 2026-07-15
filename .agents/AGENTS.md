# CIMS Workspace Context & Rules

This project is a College CRM & Institution Management System (CIMS) built using Laravel. Any agent working on this workspace must follow these instructions to align with the application architecture and code style.

## 1. System Architecture
Before proposing or making changes, read the comprehensive [technical_documentation.md](file:///c:/wamp64/www/College-crm/technical_documentation.md) which contains:
- Complete database schema references for `colleges`, `contact_persons`, `non_academic_clients`, etc.
- Development & configuration guidelines.
- Permissions and role-based access controls (RBAC).

## 2. Technology Stack & Key Packages
- **Backend Framework**: Laravel 12.x (PHP 8.2+)
- **Database**: SQLite (for in-memory testing), MySQL/MariaDB (for local development)
- **Frontend**: Blade templates, Vanilla CSS, Bootstrap 5.3, Alpine.js, Select2, DataTables (with AJAX rendering)
- **Excel/CSV Engine**: Laravel-Excel (`maatwebsite/excel`)
- **PDF Engine**: `dompdf`
- **Testing**: PHPUnit

## 3. Implementation Rules & Best Practices
- **DataTables Sorting/Searching**: When rendering DataTables server-side with Eloquent relations (e.g. `college.name`, `designation.name`), use Yajra's custom `orderColumn` callbacks to prevent SQL column name conflicts.
- **Excel Exports**: Keep filters, search words, and active column sorting in sync between the UI DataTables and the Excel download files by passing them as query string parameters to the export routes.
- **RBAC**: Always verify user status is `'active'` and validate role permissions via custom user checks (e.g. `auth()->user()->hasPermission('contacts.view')` or `@can('contacts.create')`) before allowing access to operations.
- **Test Integrity**: Ensure all unit and feature tests pass (`vendor/bin/phpunit`). When creating new test users, always specify `'status' => 'active'` so they are not auto-logged out by the global `RoleMiddleware`.
