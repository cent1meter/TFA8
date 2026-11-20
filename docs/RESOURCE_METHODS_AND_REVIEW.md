# AI-assisted resource methods and access-control review

Rainier Ian Sayson · TX33 · IT0049

## Method mapping

| Method | Purpose in customer management |
| --- | --- |
| index | List customers |
| create | Display the blank customer form |
| store | Validate and insert the submitted customer |
| show | Display one customer's details |
| edit | Display the prefilled edit form |
| update | Validate and save edits |
| destroy | Delete the selected customer |

The same responsibilities apply to users. The controllers in this project implement all seven methods, and two resource registrations replace the separate manual CRUD routes.

## Four mistakes from the actual earlier review scenario

1. **Protecting only index:** save/update/delete endpoints remain accessible through direct requests. Apply auth to every resource action, including writes.
2. **Confusing authentication with authorization:** being logged in does not establish permission to change prices or delete products. A role/policy check is required where the business requires one. This TFA8 PDF only asks for authenticated account management; the supplied project does not claim manager/cashier authorization.
3. **Sending low-stock mail before a sale commits:** a queued worker can act before the database transaction commits, or a rollback can leave a false alert. Dispatch after commit when implementing this behavior.
4. **Assuming queued mail was delivered:** without a running worker or failure monitoring, a queued notification may never be sent. Supervise workers, handle retries, and inspect failures and provider delivery information.

These refer to the earlier educational sale-notification scenario. The uploaded TFA8 PDF does not request sale/stock/email features, so this project focuses on its resource-controller and authentication requirements.

## Reflection prompts

Explain in your own words why protecting the form page alone is insufficient, how authentication differs from authorization, why database commits matter for notification accuracy, and what an email-delivery failure means for the POS's authoritative data.

## Provenance

This is an AI-assisted reference, not an independently written initial list or a record of a student test run. Attach screenshots of the genuine complete ChatGPT exchange if the separate learning activity requires them.
