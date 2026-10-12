Philippine Southfield School Computerized Library System
Presented by: Group 1 of GRADE 12 - IT 4
Last updated on: 09/10/2026 21:07:32

========================================================
                  PREREQUISITES

- A web stack solution; preferably XAMPP (as it uses MariaDB)
- A proper code editor; but optional (Visual Studio Code)
- Latest version of a modern browser (Chrome / Firefox)
   - For more accurate error checks, you'd probably go
     for Firefox, since it provides more errors than Chrome

========================================================
               FOLDER STRUCTURE

- assets/ : Files used for branding and other images
            Mostly contains logo(s) and icon(s)
- components/ : PHP scripts that are used as modularized
               components by PHP webpages
- css/ : Custom style-sheets used to customize the website
         It also modifies most of the embedded Bootstrap
- lib/ : Third-party frameworks that are used for the UI
         It contains jQuery JS & Bootstrap 3.4
- public/ : This is where user-related data is stored,
            such as images used by members for avatars
            or images for books that are dynamic.

========================================================
                    SETUP

1. Extract and move the contents to your web solution stack
   of choice. We recommend using directory that is memorable
   to you
2. Start your web stack solution and head over to
   phpMyAdmin (http://localhost/phpmyadmin)
3. Login as root, create a new database named "scls"
   and head over to the "sql" folder & drop "scls.sql"
   into the phpMyAdmin control panel while the "scls" database
   is selected.

========================================================
                  UPDATING

When updating, it is advised to back up the database itself
before proceeding to drag-n-drop the newer .sql files to avoid
import errors. If the database is in full fatal state, it will have
small chances of recovering it back to normal state, you'd probably need to
recreate the database by dropping the "scls.sql" file

========================================================
                  IMPORTANT NOTES

- A default administrator has been created by default,
   the username is "pss_admin" and the password is "icipss2025"
- Rename the project folder to "scls" in order to have
   custom error pages, this does not take in any name
- To create and use a PHP component, it must adhere that:
   - The component must act as if it's placed under the project's
      root directory (i.e. assets/book-placeholder.png)
   - When including other PHP components, use "__DIR__" then
      concatenate it with the path of the component you're
      trying to include.

========================================================
                  END OF README
