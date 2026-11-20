> Historical TFA7 validation review. TFA8 now protects resource routes with auth and adds a separate registration/login flow.

# Module 7 — Validation audit notes

Rainier Ian Sayson · TX33 · IT0049

This is an **AI-assisted revised plan**, reflecting the supplied SwiftPOS TFA6 schema and the TFA7 activity. It does not claim to be a plan independently written before the conversation. Preserve your genuine original plan separately and attach screenshots of the complete actual exchange if required by the Module 7 submission.

## Revised plan for this POS implementation

1. Customer full_name is required and limited to 100 characters. Customer email is required, must pass Laravel's email rule, and is limited to 100 characters. Phone is optional and limited to 20 characters.
2. User username is required, at most 50 characters, and unique in the users table. User full_name is required and at most 100 characters. This inherited schema has no password or user email, so the generic account/password suggestions do not apply here.
3. The existing database unique index protects usernames. During updates, the validation rule ignores only the current route-bound user. Request-supplied record IDs cannot change which record is ignored. Customer email uniqueness is not added because neither the existing schema nor this activity requires it.
4. The avatar on User Edit is optional. Server-side validation allows decoded image content corresponding to JPEG, PNG, or WebP, with a 2048 KB file limit and a 4096 × 4096 pixel bound. The HTML accept attribute is a convenience, not the security check.
5. Intervention Image/GD prepares a 256 × 256 JPEG thumbnail with metadata stripped. The selected package still needs instructor acceptance because the assignment does not identify an approved package.
6. Storage uses the public disk, UUID filenames, and relative paths in the avatar column. The original file is not published. Public URLs come from the configured disk and are escaped in Blade. A bundled placeholder is used for missing images.
7. Text inputs use old() before model defaults on redisplay, with field errors and an error summary. Browsers require the upload file to be reselected. Forms include CSRF tokens, and edit forms spoof PUT.
8. A blank upload leaves the current image unchanged. Replacement storage occurs before the database transaction. A failed save removes the new image; a successful save allows deletion of the previous generated image.

## File upload mistakes to avoid

- Trusting the browser accept attribute, supplied extension, or Content-Type alone.
- Allowing SVG/executable uploads, unlimited bytes, or unbounded image dimensions.
- Publishing the original upload instead of a prepared image.
- Using user-provided filenames or arbitrary filesystem paths.
- Saving full filesystem paths, binary file contents, or UploadedFile objects to the avatar column.
- Forgetting multipart encoding, the storage link, write permissions, or persistent hosting storage.
- Deleting the current avatar before the replacement and database update succeed.
- Expecting a browser to restore a file input after a validation failure.

## Submission checklist

- Add the actual initial validation plan with its truthful origin.
- Capture screenshots of the complete real ChatGPT exchange; none are fabricated in this project.
- Review and adapt this revised plan to your own decisions.
- Attach the mistakes list.
- For TFA7, publish the reviewed project and submit real GitHub and hosted URLs.

## AI contribution

ChatGPT reviewed the plan supplied in the conversation and helped draft the revised plan, code, tests, and documentation. Tests are supplied but were not executed in the delivery environment, which lacks PHP and Composer.
