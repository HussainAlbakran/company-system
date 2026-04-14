import { Router, type IRouter } from "express";
import { desc, eq } from "drizzle-orm";
import {
  activityItemsTable,
  approvalsTable,
  contractsTable,
  db,
  departmentsTable,
  employeesTable,
  operationalRecordsTable,
  operationalTasksTable,
  projectsTable,
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

const moneyToNumber = (value: string | number) => Number(value);

router.use(async (_req, _res, next): Promise<void> => {
  await ensureSeedData();
  await ensureOperationalRecordsSeed();
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
