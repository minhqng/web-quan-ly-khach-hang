# Full-System Audit Fix Plan

## Status
- In progress

## Scope
- Auth/session correctness for HTML and JSON endpoints.
- Customer CRUD, soft-delete, interaction/task workflow stability.
- AJAX reliability and stale filtered task rows.
- Dashboard/report data accuracy and weak empty states.
- Severe maintainability only where it blocks safe fixes.

## Priorities
1. Fix JSON endpoint session/account revalidation and duplicate-check leakage.
2. Keep soft-deleted customer state consistent with open tasks and detail history.
3. Make interaction/task date and error handling deterministic.
4. Correct dashboard/report metrics, filter labels, empty states, and misleading bars.
5. Verify PHP syntax and targeted behavior.

## Demo-Critical Fixes
- JSON endpoints reject expired, locked, or role-changed sessions.
- Soft-delete does not silently hide open tasks.
- Deleted customer detail keeps historical interactions visible to authorized users.
- Task AJAX removes rows from overdue/upcoming boards when the row no longer belongs.
- Report overview “30 days” metric is actually 30 days.
- Dashboard Top 3 excludes inactive customers.

## Validation
- Run PHP lint across all PHP files with `C:\xampp\php\php.exe -l`.
- Run targeted static searches for remaining weak patterns.
- If MySQL/Apache are available, smoke test login, dashboard, customer list AJAX, task AJAX, and reports.

## Unresolved Questions
- Whether demo credentials must remain visible on public/shared hosting.
- Whether report staff filters should mean owner or actor in every future report; current fix will label owner semantics clearly.
