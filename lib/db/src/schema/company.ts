import { integer, numeric, pgTable, serial, text, timestamp } from "drizzle-orm/pg-core";
import { createInsertSchema } from "drizzle-zod";
import { z } from "zod/v4";

export const departmentsTable = pgTable("departments", {
  id: serial("id").primaryKey(),
  name: text("name").notNull().unique(),
  description: text("description").notNull(),
  managerName: text("manager_name").notNull(),
  createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow().$onUpdate(() => new Date()),
});

export const employeesTable = pgTable("employees", {
  id: serial("id").primaryKey(),
  name: text("name").notNull(),
  email: text("email").notNull().unique(),
  phone: text("phone").notNull(),
  role: text("role").notNull(),
  department: text("department").notNull(),
  status: text("status").notNull().default("active"),
  joinedAt: timestamp("joined_at", { withTimezone: true }).notNull().defaultNow(),
  createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow().$onUpdate(() => new Date()),
});

export const projectsTable = pgTable("projects", {
  id: serial("id").primaryKey(),
  name: text("name").notNull(),
  clientName: text("client_name").notNull(),
  location: text("location").notNull(),
  stage: text("stage").notNull(),
  status: text("status").notNull().default("active"),
  progress: integer("progress").notNull().default(0),
  budget: numeric("budget", { precision: 14, scale: 2 }).notNull(),
  startsAt: timestamp("starts_at", { withTimezone: true }).notNull().defaultNow(),
  endsAt: timestamp("ends_at", { withTimezone: true }).notNull(),
  createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow().$onUpdate(() => new Date()),
});

export const contractsTable = pgTable("contracts", {
  id: serial("id").primaryKey(),
  code: text("code").notNull().unique(),
  projectName: text("project_name").notNull(),
  clientName: text("client_name").notNull(),
  value: numeric("value", { precision: 14, scale: 2 }).notNull(),
  paidAmount: numeric("paid_amount", { precision: 14, scale: 2 }).notNull().default("0"),
  status: text("status").notNull().default("active"),
  signedAt: timestamp("signed_at", { withTimezone: true }).notNull().defaultNow(),
  createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow().$onUpdate(() => new Date()),
});

export const operationalTasksTable = pgTable("operational_tasks", {
  id: serial("id").primaryKey(),
  title: text("title").notNull(),
  category: text("category").notNull(),
  projectName: text("project_name").notNull(),
  assignee: text("assignee").notNull(),
  priority: text("priority").notNull().default("medium"),
  status: text("status").notNull().default("open"),
  dueAt: timestamp("due_at", { withTimezone: true }).notNull(),
  createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow().$onUpdate(() => new Date()),
});

export const approvalsTable = pgTable("approvals", {
  id: serial("id").primaryKey(),
  title: text("title").notNull(),
  type: text("type").notNull(),
  requester: text("requester").notNull(),
  projectName: text("project_name").notNull(),
  status: text("status").notNull().default("pending"),
  createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow().$onUpdate(() => new Date()),
});

export const activityItemsTable = pgTable("activity_items", {
  id: serial("id").primaryKey(),
  title: text("title").notNull(),
  description: text("description").notNull(),
  actor: text("actor").notNull(),
  module: text("module").notNull(),
  createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
});

export const insertDepartmentSchema = createInsertSchema(departmentsTable).omit({ id: true, createdAt: true, updatedAt: true });
export const insertEmployeeSchema = createInsertSchema(employeesTable).omit({ id: true, createdAt: true, updatedAt: true, joinedAt: true, status: true });
export const insertProjectSchema = createInsertSchema(projectsTable).omit({ id: true, createdAt: true, updatedAt: true, startsAt: true, endsAt: true, status: true, progress: true });
export const insertContractSchema = createInsertSchema(contractsTable).omit({ id: true, createdAt: true, updatedAt: true });
export const insertOperationalTaskSchema = createInsertSchema(operationalTasksTable).omit({ id: true, createdAt: true, updatedAt: true, status: true, dueAt: true });
export const insertApprovalSchema = createInsertSchema(approvalsTable).omit({ id: true, createdAt: true, updatedAt: true, status: true });
export const insertActivityItemSchema = createInsertSchema(activityItemsTable).omit({ id: true });

export type Department = typeof departmentsTable.$inferSelect;
export type Employee = typeof employeesTable.$inferSelect;
export type Project = typeof projectsTable.$inferSelect;
export type Contract = typeof contractsTable.$inferSelect;
export type OperationalTask = typeof operationalTasksTable.$inferSelect;
export type Approval = typeof approvalsTable.$inferSelect;
export type ActivityItem = typeof activityItemsTable.$inferSelect;
export type InsertDepartment = z.infer<typeof insertDepartmentSchema>;
export type InsertEmployee = z.infer<typeof insertEmployeeSchema>;
export type InsertProject = z.infer<typeof insertProjectSchema>;
export type InsertContract = z.infer<typeof insertContractSchema>;
export type InsertOperationalTask = z.infer<typeof insertOperationalTaskSchema>;
export type InsertApproval = z.infer<typeof insertApprovalSchema>;
export type InsertActivityItem = z.infer<typeof insertActivityItemSchema>;
