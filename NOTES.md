Feb 8th 2026
-Created initial project directory structure for work order system: api/, config/, public/, sql/
-Configured Replit to use php82 using router.php.
-Implemented a central database connection helper (db.php) uses PDO and env variables.
-Created health check API endpoing /api/health.php to verify routing database connectivity.
-Verified PHP router is working correctly
-Database connection is verified
-Ready for auth and session management

Next steps: implement session handling and user auth - login/logout, protected dashboard view

Feb 17th 2026
- Added SQL database files into project
- Began authentication build
- Created login/logout/auth API endpoints and simple login UI
- Fixed a routing bug
- Project is ready for testing full logins
- Database and auth foundation is set up

Next to do: Test user login, verify sessions working, work on dashboard behavior.

Feb 23rd 2026
- Fixed an client-side error that was happening when login.php would load.
- Used shell to hash a new password and verified it was hashed in the database
- Tested login
- Verified password handling using password_verify() against the hashed password in the database
- Fixed a routing bug where the logout link redirected to the API path instead of the login page
- Verified logout destroys session

Next steps: Build out dashboard and UI, form/form validation, begin to work with CRUD operations and role-based permissions

3/1/2026
- Implemented dashboard "who am i" session check using /api/auth/check.php
- Built the initial dashboard summary API endpoint dashboard/summary.php
- Resolved some routing issues in router.php
- built the Create Work Order UI and API endpoint and validated form submission
- Successfully inserted new work order entry into the database tied to the logged in userID

Challenges: difficulties pushing to GitHub through Replit. Trying to use remote SSH or personal access tokens from Github doesn't seem to work, so I am having to ask the Replit Agent to push to GitHub for me.

Next steps: work on role-based behavior, improve UI, search/filtering existing work orders, work on editing/updating status changes to work orders

3/4/2026
- 