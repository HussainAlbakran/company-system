import { integer, numeric, pgTable, serial, text, timestamp } from "drizzle-orm/pg-core";
import { createInsertSchema } from "drizzle-zod";
import { z } from "zod/v4";

export const operationalRecordsTable = pgTable("operational_records", {
  id: serial("id").primaryKey(),
  module: text("module").notNull(),
  title: text("title").notNull(),
  reference: text("reference").notNull(),
  projectName: text("project_name").notNull(),
  owner: text("owner").notNull(),
  status: text("status").notNull().default("open"),
  priority: text("priority").notNull().default("medium"),
  quantity: integer("quantity").notNull().default(0),
  amount: numeric("amount", { precision: 14, scale: 2 }).notNull().default("0"),
  dueAt: timestamp("due_at", { withTimezone: true }).notNull(),
  createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow().$onUpdate(() => new Date()),
});

export const insertOperationalRecordSchema = createInsertSchema(operationalRecordsTable).omit({ id: true, createdAt: true, updatedAt: true });

export type OperationalRecord = typeof operationalRecordsTable.$inferSelect;
export type InsertOperationalRecord = z.infer<typeof insertOperationalRecordSchema>;
