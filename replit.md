# Workspace

## Overview

pnpm workspace monorepo using TypeScript. Each package manages its own dependencies.

## Stack

- **Monorepo tool**: pnpm workspaces
- **Node.js version**: 24
- **Package manager**: pnpm
- **TypeScript version**: 5.9
- **API framework**: Express 5
- **Database**: PostgreSQL + Drizzle ORM
- **Validation**: Zod (`zod/v4`), `drizzle-zod`
- **API codegen**: Orval (from OpenAPI spec)
- **Build**: esbuild (CJS bundle)
- **Frontend**: React + Vite web artifact for Company System

## Key Commands

- `pnpm run typecheck` — full typecheck across all packages
- `pnpm run build` — typecheck + build all packages
- `pnpm --filter @workspace/api-spec run codegen` — regenerate API hooks and Zod schemas from OpenAPI spec
- `pnpm --filter @workspace/db run push` — push DB schema changes (dev only)
- `pnpm --filter @workspace/api-server run dev` — run API server locally
- `pnpm --filter @workspace/company-system run dev` — run Company System web app locally

See the `pnpm-workspace` skill for workspace structure, TypeScript setup, and package details.

## Company System

- A React/Vite deployable web artifact exists at `artifacts/company-system` with preview path `/`.
- The app is an Arabic RTL system for **شركة التقدم للخرسانة الجاهزة (Advance Precast Company)**, based on the user's approved canvas mockups and existing GitHub Laravel project reference.
- Current frontend routes:
  - `/` public contracting company homepage
  - `/login` database-backed login screen
  - `/employee` employee operations dashboard
  - `/client` client project portal
  - `/admin` administrative control center
- The GitHub reference project is a Laravel system with modules for dashboard, reports, users/approvals, audit logs, employees, departments, leave requests, sales contracts/payments, architect tasks, engineering projects/updates, purchases, warehouse, factory production, installations, assets, AI assistant, and technical support.
- PostgreSQL-backed modules currently include employees, departments, projects, contracts, operational tasks, approvals, activity items, unified operational records, system users, support tickets, assistant analysis, employee document expiry alerts, and email outbox.
- The admin dashboard can create projects, approve pending approvals, create operational records, manage users and permissions, open support tickets, view reports, use a simple operational assistant, view iqama/passport expiry alerts, and queue contract-update email messages.
- The employee dashboard can approve pending approvals and mark operational tasks as completed.
- Login is now database-backed using seeded accounts with hashed passwords and an HTTP-only session cookie. The frontend also stores the selected role for route protection.
- Contract update emails are currently queued in the system email outbox. External email delivery still requires connecting a company SMTP/email provider.
- Default development accounts:
  - admin@arkan-build.com / 123456
  - employee@arkan-build.com / 123456
  - client@example.com / 123456
