import { Router, type IRouter } from "express";
import crypto from "node:crypto";
import { desc, eq } from "drizzle-orm";
import {
  activityItemsTable,
  approvalsTable,
  contractsTable,
  db,
  departmentsTable,
  emailOutboxTable,
  employeesTable,
  operationalRecordsTable,
  operationalTasksTable,
  projectsTable,
  supportTicketsTable,
  systemUsersTable,
} from "@workspace/db";
import {
  CreateEmployeeBody,
  CreateOperationalTaskBody,
  CreateOperationalRecordBody,
  CreateProjectBody,
  GetCompanyOverviewResponse,
  ListActivityResponse,
  ListApprovalsResponse,
  ListContractsResponse,
  ListEmployeesResponseItem,
  ListEmployeesResponse,
  ListOperationalTasksResponseItem,
  ListOperationalTasksResponse,
  ListOperationalRecordsResponse,
  ListOperationalRecordsResponseItem,
  ListProjectsResponseItem,
  ListProjectsResponse,
  UpdateApprovalStatusBody,
  UpdateApprovalStatusParams,
  UpdateApprovalStatusResponse,
  UpdateOperationalTaskStatusBody,
  UpdateOperationalTaskStatusParams,
  UpdateOperationalTaskStatusResponse,
} from "@workspace/api-zod";

const router: IRouter = Router();
let seedPromise: Promise<void> | null = null;

const isRecord = (value: unknown): value is Record<string, unknown> => Boolean(value) && typeof value === "object" && !Array.isArray(value);
const stringField = (body: Record<string, unknown>, key: string) => typeof body[key] === "string" ? body[key].trim() : "";
const arrayField = (body: Record<string, unknown>, key: string) => Array.isArray(body[key]) ? body[key].filter((item): item is string => typeof item === "string") : [];
const isEmail = (value: string) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
const daysUntil = (date: Date | null) => date ? Math.ceil((date.getTime() - Date.now()) / (1000 * 60 * 60 * 24)) : null;
const systemEmail = () => process.env.COMPANY_SYSTEM_EMAIL ?? "info@arkan-build.com";

const publicUser = (user: typeof systemUsersTable.$inferSelect) => ({
  id: user.id,
  name: user.name,
  email: user.email,
  role: user.role,
  department: user.department,
  status: user.status,
  permissions: JSON.parse(user.permissions) as string[],
  lastLoginAt: user.lastLoginAt?.toISOString() ?? null,
  createdAt: user.createdAt.toISOString(),
});

const hashPassword = (password: string, salt = crypto.randomBytes(16).toString("hex")) => ({
  salt,
  hash: crypto.pbkdf2Sync(password, salt, 120000, 64, "sha512").toString("hex"),
});

const verifyPassword = (password: string, salt: string, hash: string) => {
  const candidate = hashPassword(password, salt).hash;
  return crypto.timingSafeEqual(Buffer.from(candidate, "hex"), Buffer.from(hash, "hex"));
};

const sessionSecret = () => process.env.SESSION_SECRET ?? "development-session-secret";

const signSession = (userId: number) => {
  const payload = Buffer.from(JSON.stringify({ userId, exp: Date.now() + 1000 * 60 * 60 * 12 })).toString("base64url");
  const signature = crypto.createHmac("sha256", sessionSecret()).update(payload).digest("base64url");
  return `${payload}.${signature}`;
};

const readSessionUserId = (token?: string) => {
  if (!token) return null;
  const [payload, signature] = token.split(".");
  if (!payload || !signature) return null;
  const expected = crypto.createHmac("sha256", sessionSecret()).update(payload).digest("base64url");
  if (!crypto.timingSafeEqual(Buffer.from(signature), Buffer.from(expected))) return null;
  const parsed = JSON.parse(Buffer.from(payload, "base64url").toString("utf8")) as { userId: number; exp: number };
  if (!parsed.userId || parsed.exp < Date.now()) return null;
  return parsed.userId;
};

const addDays = (days: number) => {
  const date = new Date();
  date.setDate(date.getDate() + days);
  return date;
};

async function ensureSeedData(): Promise<void> {
  if (seedPromise) {
    return seedPromise;
  }

  seedPromise = (async () => {
    const existing = await db.select().from(projectsTable).limit(1);
    if (existing.length > 0) {
      return;
    }

    await db.insert(departmentsTable).values([
      { name: "الإدارة التنفيذية", description: "متابعة الأداء العام والاعتمادات", managerName: "م. حسين البكران" },
      { name: "الهندسة", description: "التصاميم والمخططات والمراجعات الفنية", managerName: "م. أحمد عبدالله" },
      { name: "المشتريات", description: "التوريد، العقود، ومتابعة الموردين", managerName: "سارة العتيبي" },
      { name: "المستودع والمصنع", description: "إدارة المخزون والإنتاج والتركيبات", managerName: "خالد العمار" },
    ]).onConflictDoNothing();

    await db.insert(employeesTable).values([
      { name: "أحمد عبدالله", email: "ahmad@arkana.build", phone: "0500001001", role: "مهندس مشروع", department: "الهندسة", status: "active", joinedAt: addDays(-480) },
      { name: "نورة القحطاني", email: "noura@arkana.build", phone: "0500001002", role: "مديرة عقود", department: "الإدارة التنفيذية", status: "active", joinedAt: addDays(-360) },
      { name: "خالد السالم", email: "khaled@arkana.build", phone: "0500001003", role: "مشرف تركيبات", department: "المستودع والمصنع", status: "active", joinedAt: addDays(-240) },
      { name: "عبدالعزيز الحربي", email: "aziz@arkana.build", phone: "0500001004", role: "مسؤول مشتريات", department: "المشتريات", status: "active", joinedAt: addDays(-180) },
    ]).onConflictDoNothing();

    await db.insert(projectsTable).values([
      { name: "مجمع فلل الياسمين", clientName: "شركة الراجحي العقارية", location: "الرياض - حي الياسمين", stage: "مرحلة العظم", status: "active", progress: 65, budget: "4200000.00", startsAt: addDays(-120), endsAt: addDays(95) },
      { name: "برج الأعمال الإداري", clientName: "مجموعة العليان", location: "الخبر", stage: "مرحلة الأساسات", status: "active", progress: 32, budget: "7600000.00", startsAt: addDays(-75), endsAt: addDays(180) },
      { name: "ترميم قصر السلام", clientName: "عميل خاص", location: "جدة", stage: "التشطيبات النهائية", status: "active", progress: 86, budget: "1850000.00", startsAt: addDays(-160), endsAt: addDays(28) },
    ]).onConflictDoNothing();

    await db.insert(contractsTable).values([
      { code: "CNT-2026-001", projectName: "مجمع فلل الياسمين", clientName: "شركة الراجحي العقارية", value: "4200000.00", paidAmount: "2520000.00", status: "active", signedAt: addDays(-125) },
      { code: "CNT-2026-002", projectName: "برج الأعمال الإداري", clientName: "مجموعة العليان", value: "7600000.00", paidAmount: "1900000.00", status: "active", signedAt: addDays(-80) },
      { code: "CNT-2026-003", projectName: "ترميم قصر السلام", clientName: "عميل خاص", value: "1850000.00", paidAmount: "1665000.00", status: "closing", signedAt: addDays(-165) },
    ]).onConflictDoNothing();

    await db.insert(operationalTasksTable).values([
      { title: "مراجعة المخطط الإنشائي المعدل", category: "الهندسة", projectName: "مجمع فلل الياسمين", assignee: "أحمد عبدالله", priority: "high", status: "open", dueAt: addDays(1) },
      { title: "اعتماد طلب توريد حديد", category: "المشتريات", projectName: "برج الأعمال الإداري", assignee: "عبدالعزيز الحربي", priority: "urgent", status: "open", dueAt: addDays(2) },
      { title: "تنسيق جدول تركيب الأبواب", category: "التركيبات", projectName: "ترميم قصر السلام", assignee: "خالد السالم", priority: "medium", status: "in_progress", dueAt: addDays(4) },
    ]).onConflictDoNothing();

    await db.insert(approvalsTable).values([
      { title: "فاتورة مقاول الباطن #1023", type: "دفعة", requester: "نورة القحطاني", projectName: "ترميم قصر السلام", status: "pending", createdAt: addDays(-1) },
      { title: "طلب إجازة - المهندس خالد", type: "موارد بشرية", requester: "خالد السالم", projectName: "الإدارة", status: "pending", createdAt: addDays(-2) },
      { title: "اعتماد أمر شراء خرسانة", type: "مشتريات", requester: "عبدالعزيز الحربي", projectName: "برج الأعمال الإداري", status: "pending", createdAt: addDays(-3) },
    ]).onConflictDoNothing();

    await db.insert(activityItemsTable).values([
      { title: "تحديث نسبة الإنجاز", description: "تم رفع تقدم مشروع ترميم قصر السلام إلى 86%", actor: "أحمد عبدالله", module: "المشاريع", createdAt: addDays(-1) },
      { title: "دفعة جديدة", description: "تسجيل دفعة بقيمة 450,000 ر.س على عقد الياسمين", actor: "نورة القحطاني", module: "العقود", createdAt: addDays(-2) },
      { title: "إدخال مستودعي", description: "تم إدخال توريد دفعة الحديد الأولى للمستودع", actor: "عبدالعزيز الحربي", module: "المستودع", createdAt: addDays(-3) },
    ]).onConflictDoNothing();

    const defaultUsers = [
      { name: "المدير العام", email: "admin@arkan-build.com", role: "admin", department: "الإدارة التنفيذية", permissions: ["all"] },
      { name: "أحمد عبدالله", email: "employee@arkan-build.com", role: "employee", department: "الهندسة", permissions: ["projects", "tasks", "approvals"] },
      { name: "بوابة العميل", email: "client@example.com", role: "client", department: "العملاء", permissions: ["client_portal"] },
    ];

    await db.insert(systemUsersTable).values(defaultUsers.map((user) => {
      const credentials = hashPassword("123456");
      return {
        ...user,
        permissions: JSON.stringify(user.permissions),
        passwordSalt: credentials.salt,
        passwordHash: credentials.hash,
      };
    })).onConflictDoNothing();

    await db.insert(supportTicketsTable).values([
      { title: "طلب صلاحية اعتماد مشتريات", requester: "عبدالعزيز الحربي", category: "الصلاحيات", priority: "high", status: "open", message: "أحتاج صلاحية اعتماد أوامر الشراء العاجلة.", assignee: "فريق الدعم" },
      { title: "مشكلة تحميل وثائق مشروع", requester: "أحمد عبدالله", category: "الوثائق", priority: "medium", status: "in_progress", message: "بعض ملفات المخططات لا تظهر في بوابة المشروع.", assignee: "الدعم الفني" },
    ]).onConflictDoNothing();
  })();

  return seedPromise;
}

async function ensureOperationalRecordsSeed(): Promise<void> {
  const existing = await db.select().from(operationalRecordsTable).limit(1);
  if (existing.length > 0) {
    return;
  }

  await db.insert(operationalRecordsTable).values([
    {
      module: "purchases",
      title: "أمر شراء خرسانة جاهزة",
      reference: "PO-2026-118",
      projectName: "برج الأعمال الإداري",
      owner: "عبدالعزيز الحربي",
      status: "pending_approval",
      priority: "urgent",
      quantity: 340,
      amount: "268000.00",
      dueAt: addDays(2),
    },
    {
      module: "purchases",
      title: "مراجعة عرض مورد الألمنيوم",
      reference: "RFQ-2026-044",
      projectName: "مجمع فلل الياسمين",
      owner: "سارة العتيبي",
      status: "under_review",
      priority: "high",
      quantity: 1,
      amount: "415000.00",
      dueAt: addDays(5),
    },
    {
      module: "warehouse",
      title: "دفعة حديد تسليح 16 مم",
      reference: "WH-IN-882",
      projectName: "برج الأعمال الإداري",
      owner: "خالد العمار",
      status: "received",
      priority: "medium",
      quantity: 28,
      amount: "196000.00",
      dueAt: addDays(-1),
    },
    {
      module: "warehouse",
      title: "تحويل مواد التشطيب للموقع",
      reference: "WH-OUT-311",
      projectName: "ترميم قصر السلام",
      owner: "خالد العمار",
      status: "scheduled",
      priority: "medium",
      quantity: 12,
      amount: "68500.00",
      dueAt: addDays(1),
    },
    {
      module: "factory",
      title: "إنتاج أبواب خشب داخلية",
      reference: "PRD-2026-057",
      projectName: "ترميم قصر السلام",
      owner: "خالد السالم",
      status: "in_progress",
      priority: "high",
      quantity: 36,
      amount: "144000.00",
      dueAt: addDays(8),
    },
    {
      module: "factory",
      title: "تصنيع درابزين السلالم",
      reference: "PRD-2026-061",
      projectName: "مجمع فلل الياسمين",
      owner: "فريق المصنع",
      status: "queued",
      priority: "medium",
      quantity: 18,
      amount: "92000.00",
      dueAt: addDays(12),
    },
    {
      module: "installations",
      title: "تركيب أبواب الدور الأرضي",
      reference: "INS-2026-204",
      projectName: "ترميم قصر السلام",
      owner: "خالد السالم",
      status: "scheduled",
      priority: "high",
      quantity: 14,
      amount: "38000.00",
      dueAt: addDays(3),
    },
    {
      module: "installations",
      title: "فحص تمديدات الكهرباء",
      reference: "INS-2026-211",
      projectName: "مجمع فلل الياسمين",
      owner: "أحمد عبدالله",
      status: "open",
      priority: "urgent",
      quantity: 6,
      amount: "22000.00",
      dueAt: addDays(1),
    },
    {
      module: "assets",
      title: "صيانة رافعة الموقع",
      reference: "AST-2026-019",
      projectName: "برج الأعمال الإداري",
      owner: "إدارة الأصول",
      status: "maintenance",
      priority: "high",
      quantity: 1,
      amount: "17500.00",
      dueAt: addDays(4),
    },
    {
      module: "assets",
      title: "تجديد تأمين سيارات الإشراف",
      reference: "AST-2026-025",
      projectName: "الإدارة",
      owner: "الإدارة التنفيذية",
      status: "pending_approval",
      priority: "medium",
      quantity: 4,
      amount: "24800.00",
      dueAt: addDays(10),
    },
    {
      module: "leaves",
      title: "طلب إجازة سنوية",
      reference: "LEV-2026-073",
      projectName: "الإدارة",
      owner: "خالد السالم",
      status: "pending_approval",
      priority: "low",
      quantity: 5,
      amount: "0.00",
      dueAt: addDays(6),
    },
    {
      module: "audit",
      title: "تعديل صلاحيات مستخدم",
      reference: "AUD-2026-301",
      projectName: "النظام",
      owner: "المدير العام",
      status: "logged",
      priority: "medium",
      quantity: 1,
      amount: "0.00",
      dueAt: addDays(0),
    },
  ]);
}

async function ensureSystemSeed(): Promise<void> {
  const existingUsers = await db.select().from(systemUsersTable).limit(1);
  if (existingUsers.length === 0) {
    const defaultUsers = [
      { name: "المدير العام", email: "admin@arkan-build.com", role: "admin", department: "الإدارة التنفيذية", permissions: ["all"] },
      { name: "أحمد عبدالله", email: "employee@arkan-build.com", role: "employee", department: "الهندسة", permissions: ["projects", "tasks", "approvals"] },
      { name: "بوابة العميل", email: "client@example.com", role: "client", department: "العملاء", permissions: ["client_portal"] },
    ];

    await db.insert(systemUsersTable).values(defaultUsers.map((user) => {
      const credentials = hashPassword("123456");
      return {
        ...user,
        permissions: JSON.stringify(user.permissions),
        passwordSalt: credentials.salt,
        passwordHash: credentials.hash,
      };
    })).onConflictDoNothing();
  }

  const existingTickets = await db.select().from(supportTicketsTable).limit(1);
  if (existingTickets.length === 0) {
    await db.insert(supportTicketsTable).values([
      { title: "طلب صلاحية اعتماد مشتريات", requester: "عبدالعزيز الحربي", category: "الصلاحيات", priority: "high", status: "open", message: "أحتاج صلاحية اعتماد أوامر الشراء العاجلة.", assignee: "فريق الدعم" },
      { title: "مشكلة تحميل وثائق مشروع", requester: "أحمد عبدالله", category: "الوثائق", priority: "medium", status: "in_progress", message: "بعض ملفات المخططات لا تظهر في بوابة المشروع.", assignee: "الدعم الفني" },
    ]).onConflictDoNothing();
  }

  const employees = await db.select().from(employeesTable);
  const expiryDates: Record<string, { iqamaExpiresAt: Date; passportExpiresAt: Date }> = {
    "ahmad@arkana.build": { iqamaExpiresAt: addDays(22), passportExpiresAt: addDays(145) },
    "noura@arkana.build": { iqamaExpiresAt: addDays(75), passportExpiresAt: addDays(38) },
    "khaled@arkana.build": { iqamaExpiresAt: addDays(12), passportExpiresAt: addDays(260) },
    "aziz@arkana.build": { iqamaExpiresAt: addDays(105), passportExpiresAt: addDays(18) },
  };
  await Promise.all(employees.map((employee) => {
    const dates = expiryDates[employee.email];
    if (!dates) return Promise.resolve();
    return db.update(employeesTable).set(dates).where(eq(employeesTable.id, employee.id));
  }));
}

const moneyToNumber = (value: string | number) => Number(value);

router.use(async (_req, _res, next): Promise<void> => {
  await ensureSeedData();
  await ensureOperationalRecordsSeed();
  await ensureSystemSeed();
  next();
});

router.get("/company/overview", async (_req, res): Promise<void> => {
  const [projects, approvals, contracts, departments] = await Promise.all([
    db.select().from(projectsTable),
    db.select().from(approvalsTable),
    db.select().from(contractsTable),
    db.select().from(departmentsTable),
  ]);

  const activeProjects = projects.filter((project) => project.status === "active").length;
  const pendingApprovals = approvals.filter((approval) => approval.status === "pending").length;
  const totalProgress = projects.reduce((sum, project) => sum + project.progress, 0);
  const monthlyCompletion = projects.length ? Math.round(totalProgress / projects.length) : 0;
  const totalContractValue = contracts.reduce((sum, contract) => sum + moneyToNumber(contract.value), 0);

  res.json(GetCompanyOverviewResponse.parse({
    activeProjects,
    pendingApprovals,
    monthlyCompletion,
    totalContractValue,
    metrics: [
      { label: "المشاريع النشطة", value: String(activeProjects), change: "+2 هذا الشهر" },
      { label: "الموافقات المعلقة", value: String(pendingApprovals), change: "تحتاج إجراء" },
      { label: "إنجاز الشهر", value: `${monthlyCompletion}%`, change: "أعلى من المخطط" },
      { label: "قيمة العقود", value: `${Math.round(totalContractValue / 1000000)}م`, change: "ريال سعودي" },
    ],
    departmentBreakdown: departments.map((department) => ({
      label: department.name,
      value: department.managerName,
      change: department.description,
    })),
  }));
});

router.get("/company/employees", async (_req, res): Promise<void> => {
  const employees = await db.select().from(employeesTable).orderBy(desc(employeesTable.joinedAt));
  res.json(ListEmployeesResponse.parse(employees));
});

router.post("/company/employees", async (req, res): Promise<void> => {
  const parsed = CreateEmployeeBody.safeParse(req.body);
  if (!parsed.success) {
    req.log.warn({ errors: parsed.error.message }, "Invalid employee payload");
    res.status(400).json({ error: parsed.error.message });
    return;
  }

  const [employee] = await db.insert(employeesTable).values(parsed.data).returning();
  res.status(201).json(ListEmployeesResponseItem.parse(employee));
});

router.get("/company/projects", async (_req, res): Promise<void> => {
  const projects = await db.select().from(projectsTable).orderBy(desc(projectsTable.createdAt));
  res.json(ListProjectsResponse.parse(projects.map((project) => ({ ...project, budget: moneyToNumber(project.budget) }))));
});

router.post("/company/projects", async (req, res): Promise<void> => {
  const parsed = CreateProjectBody.safeParse(req.body);
  if (!parsed.success) {
    req.log.warn({ errors: parsed.error.message }, "Invalid project payload");
    res.status(400).json({ error: parsed.error.message });
    return;
  }

  const [project] = await db.insert(projectsTable).values({
    ...parsed.data,
    budget: String(parsed.data.budget),
    endsAt: addDays(180),
  }).returning();
  res.status(201).json(ListProjectsResponseItem.parse({ ...project, budget: moneyToNumber(project.budget) }));
});

router.get("/company/contracts", async (_req, res): Promise<void> => {
  const contracts = await db.select().from(contractsTable).orderBy(desc(contractsTable.signedAt));
  res.json(ListContractsResponse.parse(contracts.map((contract) => ({
    ...contract,
    value: moneyToNumber(contract.value),
    paidAmount: moneyToNumber(contract.paidAmount),
  }))));
});

router.post("/company/contracts/:id/email-update", async (req, res): Promise<void> => {
  const id = Number(req.params.id);
  if (!Number.isInteger(id) || id <= 0 || !isRecord(req.body)) {
    res.status(400).json({ error: "بيانات تحديث العقد غير صحيحة" });
    return;
  }

  const [contract] = await db.select().from(contractsTable).where(eq(contractsTable.id, id)).limit(1);
  if (!contract) {
    res.status(404).json({ error: "العقد غير موجود" });
    return;
  }

  const toEmail = stringField(req.body, "toEmail") || "client@example.com";
  const movement = stringField(req.body, "movement") || "تحديث حالة العقد";
  const details = stringField(req.body, "details") || `تم تحديث عقد ${contract.code} لمشروع ${contract.projectName}.`;
  if (!isEmail(toEmail)) {
    res.status(400).json({ error: "البريد الإلكتروني غير صحيح" });
    return;
  }

  const [email] = await db.insert(emailOutboxTable).values({
    fromEmail: systemEmail(),
    toEmail,
    subject: `تحديث عقد ${contract.code} - ${contract.projectName}`,
    body: `مرحباً،\n\n${movement}\n${details}\n\nشركة أركانا البناء`,
    relatedModule: "contracts",
    relatedId: contract.id,
    createdBy: "المدير العام",
  }).returning();

  await db.insert(activityItemsTable).values({
    title: "تجهيز بريد تحديث عقد",
    description: `تم تجهيز رسالة بريد لتحديث العقد ${contract.code}`,
    actor: "المدير العام",
    module: "العقود",
    createdAt: new Date(),
  });

  res.status(201).json(email);
});

router.get("/company/tasks", async (_req, res): Promise<void> => {
  const tasks = await db.select().from(operationalTasksTable).orderBy(desc(operationalTasksTable.dueAt));
  res.json(ListOperationalTasksResponse.parse(tasks));
});

router.post("/company/tasks", async (req, res): Promise<void> => {
  const parsed = CreateOperationalTaskBody.safeParse(req.body);
  if (!parsed.success) {
    req.log.warn({ errors: parsed.error.message }, "Invalid task payload");
    res.status(400).json({ error: parsed.error.message });
    return;
  }

  const [task] = await db.insert(operationalTasksTable).values({
    ...parsed.data,
    dueAt: addDays(7),
  }).returning();
  res.status(201).json(ListOperationalTasksResponseItem.parse(task));
});

router.patch("/company/tasks/:id/status", async (req, res): Promise<void> => {
  const params = UpdateOperationalTaskStatusParams.safeParse(req.params);
  if (!params.success) {
    res.status(400).json({ error: params.error.message });
    return;
  }

  const parsed = UpdateOperationalTaskStatusBody.safeParse(req.body);
  if (!parsed.success) {
    req.log.warn({ errors: parsed.error.message }, "Invalid task status payload");
    res.status(400).json({ error: parsed.error.message });
    return;
  }

  const [task] = await db
    .update(operationalTasksTable)
    .set({ status: parsed.data.status })
    .where(eq(operationalTasksTable.id, params.data.id))
    .returning();

  if (!task) {
    res.status(404).json({ error: "Task not found" });
    return;
  }

  await db.insert(activityItemsTable).values({
    title: "تحديث حالة مهمة",
    description: `تم تغيير حالة مهمة ${task.title} إلى ${parsed.data.status}`,
    actor: task.assignee,
    module: "المهام",
    createdAt: new Date(),
  });

  res.json(UpdateOperationalTaskStatusResponse.parse(task));
});

router.get("/company/approvals", async (_req, res): Promise<void> => {
  const approvals = await db.select().from(approvalsTable).orderBy(desc(approvalsTable.createdAt));
  res.json(ListApprovalsResponse.parse(approvals));
});

router.patch("/company/approvals/:id/status", async (req, res): Promise<void> => {
  const params = UpdateApprovalStatusParams.safeParse(req.params);
  if (!params.success) {
    res.status(400).json({ error: params.error.message });
    return;
  }

  const parsed = UpdateApprovalStatusBody.safeParse(req.body);
  if (!parsed.success) {
    req.log.warn({ errors: parsed.error.message }, "Invalid approval status payload");
    res.status(400).json({ error: parsed.error.message });
    return;
  }

  const [approval] = await db
    .update(approvalsTable)
    .set({ status: parsed.data.status })
    .where(eq(approvalsTable.id, params.data.id))
    .returning();

  if (!approval) {
    res.status(404).json({ error: "Approval not found" });
    return;
  }

  await db.insert(activityItemsTable).values({
    title: "تحديث موافقة",
    description: `تم تغيير حالة ${approval.title} إلى ${parsed.data.status}`,
    actor: approval.requester,
    module: "الموافقات",
    createdAt: new Date(),
  });

  res.json(UpdateApprovalStatusResponse.parse(approval));
});

router.get("/company/activity", async (_req, res): Promise<void> => {
  const activity = await db.select().from(activityItemsTable).orderBy(desc(activityItemsTable.createdAt));
  res.json(ListActivityResponse.parse(activity));
});

router.get("/company/email-outbox", async (_req, res): Promise<void> => {
  const emails = await db.select().from(emailOutboxTable).orderBy(desc(emailOutboxTable.createdAt));
  res.json(emails);
});

router.get("/company/email-settings", async (_req, res): Promise<void> => {
  res.json({
    systemEmail: systemEmail(),
    deliveryConfigured: Boolean(process.env.SMTP_HOST && process.env.SMTP_USER && process.env.SMTP_PASS),
    deliveryProvider: process.env.SMTP_HOST ? "SMTP" : null,
  });
});

router.get("/company/alerts", async (_req, res): Promise<void> => {
  const [employees, emails] = await Promise.all([
    db.select().from(employeesTable).orderBy(desc(employeesTable.joinedAt)),
    db.select().from(emailOutboxTable).orderBy(desc(emailOutboxTable.createdAt)),
  ]);

  const documentAlerts = employees.flatMap((employee) => {
    const alerts = [];
    const iqamaDays = daysUntil(employee.iqamaExpiresAt);
    const passportDays = daysUntil(employee.passportExpiresAt);
    if (iqamaDays !== null && iqamaDays <= 60) {
      alerts.push({
        id: `iqama-${employee.id}`,
        type: "iqama",
        title: `انتهاء إقامة ${employee.name}`,
        employeeName: employee.name,
        employeeEmail: employee.email,
        expiresAt: employee.iqamaExpiresAt?.toISOString(),
        daysRemaining: iqamaDays,
        severity: iqamaDays <= 15 ? "urgent" : "warning",
      });
    }
    if (passportDays !== null && passportDays <= 60) {
      alerts.push({
        id: `passport-${employee.id}`,
        type: "passport",
        title: `انتهاء جواز ${employee.name}`,
        employeeName: employee.name,
        employeeEmail: employee.email,
        expiresAt: employee.passportExpiresAt?.toISOString(),
        daysRemaining: passportDays,
        severity: passportDays <= 15 ? "urgent" : "warning",
      });
    }
    return alerts;
  });

  res.json({
    documentAlerts,
    queuedEmails: emails.filter((email) => email.status === "queued"),
    counts: {
      documentAlerts: documentAlerts.length,
      queuedEmails: emails.filter((email) => email.status === "queued").length,
    },
  });
});

router.post("/company/auth/login", async (req, res): Promise<void> => {
  if (!isRecord(req.body)) {
    res.status(400).json({ error: "بيانات الدخول غير صحيحة" });
    return;
  }
  const email = stringField(req.body, "email").toLowerCase();
  const password = stringField(req.body, "password");
  const role = stringField(req.body, "role");
  if (!isEmail(email) || !password) {
    res.status(400).json({ error: "بيانات الدخول غير صحيحة" });
    return;
  }

  const [user] = await db.select().from(systemUsersTable).where(eq(systemUsersTable.email, email)).limit(1);
  if (!user || user.status !== "active" || !verifyPassword(password, user.passwordSalt, user.passwordHash) || (role && user.role !== role)) {
    res.status(401).json({ error: "البريد أو كلمة المرور غير صحيحة" });
    return;
  }

  const [updatedUser] = await db.update(systemUsersTable).set({ lastLoginAt: new Date() }).where(eq(systemUsersTable.id, user.id)).returning();
  res.cookie("arkana_session", signSession(user.id), {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    maxAge: 1000 * 60 * 60 * 12,
  });
  res.json({ user: publicUser(updatedUser ?? user) });
});

router.post("/company/auth/logout", async (_req, res): Promise<void> => {
  res.clearCookie("arkana_session");
  res.json({ ok: true });
});

router.get("/company/auth/me", async (req, res): Promise<void> => {
  const userId = readSessionUserId(req.cookies?.arkana_session);
  if (!userId) {
    res.status(401).json({ error: "غير مسجل الدخول" });
    return;
  }
  const [user] = await db.select().from(systemUsersTable).where(eq(systemUsersTable.id, userId)).limit(1);
  if (!user || user.status !== "active") {
    res.status(401).json({ error: "غير مسجل الدخول" });
    return;
  }
  res.json({ user: publicUser(user) });
});

router.get("/company/users", async (_req, res): Promise<void> => {
  const users = await db.select().from(systemUsersTable).orderBy(desc(systemUsersTable.createdAt));
  res.json(users.map(publicUser));
});

router.post("/company/users", async (req, res): Promise<void> => {
  if (!isRecord(req.body)) {
    res.status(400).json({ error: "بيانات المستخدم غير صحيحة" });
    return;
  }
  const name = stringField(req.body, "name");
  const email = stringField(req.body, "email").toLowerCase();
  const role = stringField(req.body, "role");
  const department = stringField(req.body, "department");
  const password = stringField(req.body, "password") || "123456";
  const permissions = arrayField(req.body, "permissions");
  if (name.length < 2 || !isEmail(email) || !["admin", "employee", "client"].includes(role) || department.length < 2 || password.length < 6) {
    res.status(400).json({ error: "بيانات المستخدم غير صحيحة" });
    return;
  }

  const credentials = hashPassword(password);
  const [user] = await db.insert(systemUsersTable).values({
    name,
    email,
    role,
    department,
    permissions: JSON.stringify(permissions),
    passwordSalt: credentials.salt,
    passwordHash: credentials.hash,
  }).returning();

  await db.insert(activityItemsTable).values({
    title: "إضافة مستخدم",
    description: `تم إضافة المستخدم ${user.name} بصلاحية ${user.role}`,
    actor: "المدير العام",
    module: "المستخدمين",
    createdAt: new Date(),
  });

  res.status(201).json(publicUser(user));
});

router.get("/company/support", async (_req, res): Promise<void> => {
  const tickets = await db.select().from(supportTicketsTable).orderBy(desc(supportTicketsTable.createdAt));
  res.json(tickets);
});

router.post("/company/support", async (req, res): Promise<void> => {
  if (!isRecord(req.body)) {
    res.status(400).json({ error: "بيانات التذكرة غير صحيحة" });
    return;
  }
  const title = stringField(req.body, "title");
  const requester = stringField(req.body, "requester");
  const category = stringField(req.body, "category");
  const priority = stringField(req.body, "priority") || "medium";
  const message = stringField(req.body, "message");
  if (title.length < 3 || requester.length < 2 || category.length < 2 || message.length < 3) {
    res.status(400).json({ error: "بيانات التذكرة غير صحيحة" });
    return;
  }

  const [ticket] = await db.insert(supportTicketsTable).values({ title, requester, category, priority, message }).returning();
  await db.insert(activityItemsTable).values({
    title: "تذكرة دعم جديدة",
    description: `تم فتح تذكرة ${ticket.title}`,
    actor: ticket.requester,
    module: "الدعم الفني",
    createdAt: new Date(),
  });
  res.status(201).json(ticket);
});

router.post("/company/assistant/ask", async (req, res): Promise<void> => {
  if (!isRecord(req.body)) {
    res.status(400).json({ error: "أدخل سؤالاً واضحاً" });
    return;
  }
  const question = stringField(req.body, "question");
  if (question.length < 2) {
    res.status(400).json({ error: "أدخل سؤالاً واضحاً" });
    return;
  }

  const [projects, approvals, tasks, operations] = await Promise.all([
    db.select().from(projectsTable),
    db.select().from(approvalsTable),
    db.select().from(operationalTasksTable),
    db.select().from(operationalRecordsTable),
  ]);
  const delayedTasks = tasks.filter((task) => task.status !== "completed" && task.dueAt < new Date()).length;
  const pendingApprovals = approvals.filter((approval) => approval.status === "pending").length;
  const urgentOperations = operations.filter((record) => ["urgent", "high"].includes(record.priority) && ["open", "pending_approval", "under_review"].includes(record.status)).length;
  const averageProgress = projects.length ? Math.round(projects.reduce((sum, project) => sum + project.progress, 0) / projects.length) : 0;

  res.json({
    answer: `ملخص سريع: متوسط إنجاز المشاريع ${averageProgress}%، يوجد ${pendingApprovals} موافقات معلقة، ${delayedTasks} مهام متأخرة، و${urgentOperations} سجلات تشغيلية عالية الأولوية. التوصية: ابدأ بالموافقات المالية والمشتريات العاجلة ثم راجع المهام المتأخرة حسب المشروع.`,
    question,
  });
});

router.get("/company/operations", async (_req, res): Promise<void> => {
  const records = await db
    .select()
    .from(operationalRecordsTable)
    .orderBy(desc(operationalRecordsTable.createdAt));
  res.json(ListOperationalRecordsResponse.parse(records.map((record) => ({
    ...record,
    amount: moneyToNumber(record.amount),
  }))));
});

router.post("/company/operations", async (req, res): Promise<void> => {
  const parsed = CreateOperationalRecordBody.safeParse(req.body);
  if (!parsed.success) {
    req.log.warn({ errors: parsed.error.message }, "Invalid operational record payload");
    res.status(400).json({ error: parsed.error.message });
    return;
  }

  const [record] = await db.insert(operationalRecordsTable).values({
    ...parsed.data,
    amount: String(parsed.data.amount),
  }).returning();

  await db.insert(activityItemsTable).values({
    title: "إضافة سجل تشغيلي",
    description: `تم إضافة ${record.title} في وحدة ${record.module}`,
    actor: record.owner,
    module: record.module,
    createdAt: new Date(),
  });

  res.status(201).json(ListOperationalRecordsResponseItem.parse({
    ...record,
    amount: moneyToNumber(record.amount),
  }));
});

export default router;
