The repository employs a hybrid error handling strategy leveraging Laravel's centralized exception configuration and Inertia.js's client-side validation feedback loop. 

### Backend Exception Management
- **Centralized Configuration**: Exception rendering and reporting are configured in `bootstrap/app.php` using the `withExceptions` closure. This approach is preferred over co-location on exception classes for this project.
- **JSON Rendering**: The application explicitly forces JSON error rendering for requests matching `api/*` to ensure consistent API responses, while relying on Laravel's default detection for other routes.
- **Validation**: Form requests (e.g., `StoreApplicantProfileRequest`) handle validation errors automatically by redirecting back with error messages, which are then made available to Inertia pages via the `errors` prop.
- **Authorization**: Simple authorization failures are handled using `abort(403)` in controllers, which triggers Laravel's standard error response flow.

### Frontend Error Presentation
- **Validation Errors**: The `InputError.vue` component is used extensively within forms to display field-specific validation messages passed down from the backend via Inertia's shared `errors` object.
- **Global Alerts**: The `AlertError.vue` component provides a standardized UI for displaying arrays of general errors, often used for non-field-specific issues or API failures.
- **Flash Messaging**: Success and informational messages are managed via `Inertia::flash()`. The `flashToast.ts` composable listens for these flash events and presents them using the `vue-sonner` toast library, ensuring a non-intrusive user experience.

### Developer Conventions
1. **Use Form Requests**: Always use dedicated Form Request classes for validation to keep controllers clean and leverage automatic error redirection.
2. **Centralize Exception Logic**: Add custom exception rendering or reporting logic to `bootstrap/app.php` rather than creating new exception classes with `render` methods, unless the logic is highly specific and reusable.
3. **Consistent UI Components**: Use `InputError` for form fields and `AlertError` for page-level or modal-level error summaries. Do not create custom error display logic in individual pages.
4. **Flash for Success**: Use `Inertia::flash('toast', ...)` for success messages to maintain a consistent notification pattern across the application.