import React, { useState } from "react";
import { useLocation } from "wouter";
import { 
  Building2, Search, Bell, Menu, LayoutDashboard, FileText, Users, 
  Settings, FolderOpen, PieChart, ShieldCheck, Box, Factory, Wrench, 
  Car, MessageSquare, Briefcase, LogOut, ChevronLeft, Plus
} from "lucide-react";
import { useGetCompanyOverview, useListActivity, useListApprovals, useListEmployees, useListProjects, useListContracts, useCreateProject } from "@workspace/api-client-react";
import { format } from "date-fns";
import { ar } from "date-fns/locale";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { useToast } from "@/hooks/use-toast";
import { useQueryClient } from "@tanstack/react-query";
import { getListProjectsQueryKey } from "@workspace/api-client-react";

export function AdminDashboard() {
  const [, setLocation] = useLocation();
  const [activeModule, setActiveModule] = useState("dashboard");
  const { toast } = useToast();
  const queryClient = useQueryClient();

  const { data: overview, isLoading: isLoadingOverview } = useGetCompanyOverview();
  const { data: activity, isLoading: isLoadingActivity } = useListActivity();
  const { data: approvals, isLoading: isLoadingApprovals } = useListApprovals();
  const { data: employees, isLoading: isLoadingEmployees } = useListEmployees();
  const { data: projects, isLoading: isLoadingProjects } = useListProjects();
  const { data: contracts, isLoading: isLoadingContracts } = useListContracts();
  
  const createProjectMutation = useCreateProject({
    mutation: {
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: getListProjectsQueryKey() });
        toast({ title: "تم إضافة المشروع بنجاح" });
        setIsCreateProjectOpen(false);
      },
      onError: () => {
        toast({ title: "حدث خطأ أثناء إضافة المشروع", variant: "destructive" });
      }
    }
  });

  const [isCreateProjectOpen, setIsCreateProjectOpen] = useState(false);
  const [newProject, setNewProject] = useState({ name: "", clientName: "", location: "", stage: "planning", budget: 0 });

  const handleLogout = () => {
    localStorage.removeItem("userRole");
    setLocation("/login");
  };

  const modules = [
    { id: "dashboard", icon: <LayoutDashboard />, label: "لوحة التحكم" },
    { id: "projects", icon: <Briefcase />, label: "المشاريع الهندسية" },
    { id: "employees", icon: <Users />, label: "الموظفين والرواتب" },
    { id: "contracts", icon: <FileText />, label: "العقود والمقاولين" },
    { id: "reports", icon: <PieChart />, label: "التقارير" },
    { id: "users", icon: <ShieldCheck />, label: "المستخدمين والصلاحيات" },
    { id: "purchases", icon: <FolderOpen />, label: "المشتريات والطلبات" },
    { id: "warehouse", icon: <Box />, label: "المستودعات والمخزون" },
    { id: "factory", icon: <Factory />, label: "المصنع والإنتاج" },
    { id: "installations", icon: <Wrench />, label: "التركيبات والصيانة" },
    { id: "assets", icon: <Car />, label: "الأصول والسيارات" },
    { id: "support", icon: <MessageSquare />, label: "الدعم الفني" },
  ];

  return (
    <div dir="rtl" className="min-h-screen bg-slate-50 font-sans text-slate-900 flex selection:bg-amber-500 selection:text-slate-900">
      
      {/* Sidebar */}
      <aside className="w-64 bg-slate-900 text-slate-300 hidden lg:flex flex-col border-l border-slate-800 shrink-0">
        <div className="h-20 flex items-center px-6 border-b border-slate-800">
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 bg-amber-500 rounded flex items-center justify-center">
              <Building2 className="text-slate-900 w-5 h-5" />
            </div>
            <span className="font-bold text-xl tracking-tight text-white">أركان<span className="text-amber-500">البناء</span></span>
          </div>
        </div>

        <div className="flex-1 py-6 px-3 space-y-1 overflow-y-auto custom-scrollbar">
          <div className="text-xs font-semibold text-slate-500 px-3 mb-4 mt-2">الوحدات التشغيلية</div>
          
          {modules.map((mod) => (
            <button 
              key={mod.id}
              onClick={() => setActiveModule(mod.id)}
              data-testid={`module-${mod.id}`}
              className={`w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors ${
                activeModule === mod.id 
                  ? 'bg-amber-500/10 text-amber-500 font-bold' 
                  : 'hover:bg-slate-800 text-slate-300 hover:text-white font-medium'
              }`}
            >
              {React.cloneElement(mod.icon, { className: "w-5 h-5" })}
              {mod.label}
            </button>
          ))}
          
          <div className="text-xs font-semibold text-slate-500 px-3 mb-4 mt-8">النظام</div>
          
          <button className="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors font-medium">
            <Settings className="w-5 h-5" />
            إعدادات النظام
          </button>
        </div>

        <div className="p-4 border-t border-slate-800">
          <div className="flex items-center gap-3 bg-slate-800/50 p-3 rounded-xl border border-slate-700/50">
            <div className="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center font-bold text-slate-900 border-2 border-slate-600">
              إ
            </div>
            <div className="flex-1 min-w-0">
              <div className="text-sm font-semibold text-white truncate">المدير العام</div>
              <div className="text-xs text-slate-400 truncate">صلاحيات كاملة</div>
            </div>
            <button onClick={handleLogout} className="text-slate-500 hover:text-red-400 p-1" data-testid="button-logout-admin">
              <LogOut className="w-4 h-4" />
            </button>
          </div>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        
        {/* Header */}
        <header className="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-8 shrink-0 z-10 shadow-sm">
          <div className="flex items-center gap-4">
            <button className="lg:hidden text-slate-500 hover:text-slate-900">
              <Menu className="w-6 h-6" />
            </button>
            <h1 className="text-2xl font-bold text-slate-900 hidden sm:block">مركز التحكم والعمليات</h1>
          </div>

          <div className="flex items-center gap-4 sm:gap-6">
            <div className="relative hidden md:block w-64">
              <div className="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <Search className="w-4 h-4 text-slate-400" />
              </div>
              <input 
                type="text" 
                className="w-full bg-slate-50 border border-slate-200 rounded-full pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all" 
                placeholder="بحث عام في النظام..." 
              />
            </div>
            
            <button className="relative p-2 text-slate-400 hover:text-slate-900 transition-colors rounded-full hover:bg-slate-50">
              <Bell className="w-5 h-5" />
              <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-amber-500 rounded-full border-2 border-white"></span>
            </button>

            <button className="lg:hidden relative p-2 text-slate-400 hover:text-red-500 transition-colors rounded-full hover:bg-slate-50" onClick={handleLogout}>
              <LogOut className="w-5 h-5" />
            </button>
          </div>
        </header>

        {/* Scrollable Content */}
        <div className="flex-1 overflow-y-auto p-4 sm:p-8 bg-slate-50/50">
          <div className="max-w-7xl mx-auto space-y-8">
            
            {/* Module Title */}
            <div className="flex items-center justify-between mb-6">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
                  {modules.find(m => m.id === activeModule)?.icon}
                </div>
                <h2 className="text-2xl font-bold text-slate-900">{modules.find(m => m.id === activeModule)?.label}</h2>
              </div>

              {activeModule === "projects" && (
                <Dialog open={isCreateProjectOpen} onOpenChange={setIsCreateProjectOpen}>
                  <DialogTrigger asChild>
                    <Button className="bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold gap-2">
                      <Plus className="w-4 h-4" /> إضافة مشروع
                    </Button>
                  </DialogTrigger>
                  <DialogContent dir="rtl" className="font-sans">
                    <DialogHeader>
                      <DialogTitle>إضافة مشروع جديد</DialogTitle>
                    </DialogHeader>
                    <div className="space-y-4 pt-4">
                      <div className="space-y-2">
                        <Label>اسم المشروع</Label>
                        <Input value={newProject.name} onChange={e => setNewProject({...newProject, name: e.target.value})} placeholder="مثال: مجمع سكني الفلاح" />
                      </div>
                      <div className="space-y-2">
                        <Label>العميل</Label>
                        <Input value={newProject.clientName} onChange={e => setNewProject({...newProject, clientName: e.target.value})} placeholder="اسم العميل أو الشركة" />
                      </div>
                      <div className="space-y-2">
                        <Label>الموقع</Label>
                        <Input value={newProject.location} onChange={e => setNewProject({...newProject, location: e.target.value})} placeholder="مدينة، حي" />
                      </div>
                      <div className="space-y-2">
                        <Label>الميزانية (ريال)</Label>
                        <Input type="number" dir="ltr" value={newProject.budget} onChange={e => setNewProject({...newProject, budget: Number(e.target.value)})} />
                      </div>
                      <Button 
                        className="w-full bg-slate-900 text-white hover:bg-slate-800 mt-2" 
                        disabled={createProjectMutation.isPending}
                        onClick={() => createProjectMutation.mutate({ data: newProject })}
                      >
                        {createProjectMutation.isPending ? "جاري الإضافة..." : "حفظ المشروع"}
                      </Button>
                    </div>
                  </DialogContent>
                </Dialog>
              )}
            </div>

            {/* Dashboard Overview */}
            {activeModule === "dashboard" && (
              <>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                  {[
                    { label: "إجمالي المشاريع", value: isLoadingOverview ? "..." : overview?.activeProjects || "48", desc: "نشط حالياً", color: "bg-blue-500" },
                    { label: "الموافقات المعلقة", value: isLoadingOverview ? "..." : overview?.pendingApprovals || "342", desc: "في انتظار الاعتماد", color: "bg-emerald-500" },
                    { label: "نسبة الإنجاز الشهري", value: isLoadingOverview ? "..." : `${overview?.monthlyCompletion || 0}%`, desc: "متوسط إنجاز المشاريع", color: "bg-amber-500" },
                    { label: "إجمالي قيمة العقود", value: isLoadingOverview ? "..." : `${(overview?.totalContractValue || 0).toLocaleString()} ريال`, desc: "تراكمي", color: "bg-red-500" }
                  ].map((stat, i) => (
                    <div key={i} className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group">
                      <div className="relative z-10">
                        <div className="text-slate-500 text-sm font-medium mb-2">{stat.label}</div>
                        <div className="text-4xl font-bold text-slate-900 mb-2" dir="ltr">{stat.value}</div>
                        <div className="text-sm font-medium text-slate-500">{stat.desc}</div>
                      </div>
                      <div className={`absolute bottom-0 right-0 w-full h-1 ${stat.color}`}></div>
                    </div>
                  ))}
                </div>

                <div className="grid lg:grid-cols-2 gap-8">
                  <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 className="text-lg font-bold text-slate-900 mb-4">أحدث النشاطات</h3>
                    <div className="space-y-4">
                      {isLoadingActivity ? (
                        <div className="text-center text-sm text-slate-500 py-8">جاري التحميل...</div>
                      ) : activity && activity.length > 0 ? (
                        activity.slice(0, 5).map((log, i) => (
                          <div key={i} className="flex gap-4 items-start pb-4 border-b border-slate-100 last:border-0 last:pb-0">
                            <div className="w-2 h-2 rounded-full bg-amber-500 mt-2 shrink-0"></div>
                            <div>
                              <div className="font-semibold text-slate-800 text-sm">{log.title}</div>
                              <div className="text-xs text-slate-500 mt-1">{format(new Date(log.createdAt), "dd MMM yyyy", { locale: ar })} • {log.actor}</div>
                            </div>
                          </div>
                        ))
                      ) : (
                        <div className="text-center text-sm text-slate-500 py-8">لا توجد نشاطات</div>
                      )}
                    </div>
                  </div>

                  <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 className="text-lg font-bold text-slate-900 mb-4">موافقات إدارية معلقة</h3>
                    <div className="space-y-3">
                      {isLoadingApprovals ? (
                        <div className="text-center text-sm text-slate-500 py-8">جاري التحميل...</div>
                      ) : approvals && approvals.length > 0 ? (
                        approvals.slice(0, 5).map((app, i) => (
                          <div key={i} className="flex items-center justify-between p-3 rounded-lg border border-slate-100 hover:border-amber-200 transition-colors bg-slate-50/50">
                            <div>
                              <div className="font-bold text-slate-800 text-sm mb-1">{app.title}</div>
                              <div className="text-xs font-medium text-slate-500">{app.requester} - {app.projectName}</div>
                            </div>
                            <div className="flex items-center gap-3">
                              <span className={`text-xs px-2 py-1 rounded font-semibold ${app.type === 'financial' || app.type === 'مالي' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'}`}>
                                {app.type}
                              </span>
                              <button className="text-amber-600 hover:bg-amber-50 p-1.5 rounded">
                                <ChevronLeft className="w-5 h-5" />
                              </button>
                            </div>
                          </div>
                        ))
                      ) : (
                        <div className="text-center text-sm text-slate-500 py-8">لا توجد موافقات معلقة</div>
                      )}
                    </div>
                  </div>
                </div>
              </>
            )}

            {/* Employees View */}
            {activeModule === "employees" && (
              <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div className="p-6 border-b border-slate-100 flex justify-between items-center">
                  <h3 className="text-lg font-bold text-slate-900">قائمة الموظفين</h3>
                  <div className="relative">
                    <Search className="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2" />
                    <input type="text" placeholder="بحث بالاسم أو القسم..." className="pl-4 pr-10 py-2 border border-slate-200 rounded-lg text-sm" />
                  </div>
                </div>
                <div className="overflow-x-auto">
                  <table className="w-full text-right">
                    <thead className="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm">
                      <tr>
                        <th className="px-6 py-4 font-semibold">الموظف</th>
                        <th className="px-6 py-4 font-semibold">الدور</th>
                        <th className="px-6 py-4 font-semibold">القسم</th>
                        <th className="px-6 py-4 font-semibold">الحالة</th>
                        <th className="px-6 py-4 font-semibold">تاريخ الانضمام</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                      {isLoadingEmployees ? (
                        <tr><td colSpan={5} className="p-8 text-center text-slate-500">جاري التحميل...</td></tr>
                      ) : employees && employees.length > 0 ? (
                        employees.map((emp) => (
                          <tr key={emp.id} className="hover:bg-slate-50/50 transition-colors">
                            <td className="px-6 py-4">
                              <div className="font-semibold text-slate-900">{emp.name}</div>
                              <div className="text-xs text-slate-500 font-sans" dir="ltr">{emp.email}</div>
                            </td>
                            <td className="px-6 py-4 text-slate-700">{emp.role}</td>
                            <td className="px-6 py-4 text-slate-700">{emp.department}</td>
                            <td className="px-6 py-4">
                              <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold ${
                                emp.status === 'active' || emp.status === 'نشط' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'
                              }`}>
                                {emp.status === 'active' ? 'نشط' : emp.status}
                              </span>
                            </td>
                            <td className="px-6 py-4 text-slate-500 text-sm">
                              {format(new Date(emp.joinedAt), "dd MMM yyyy", { locale: ar })}
                            </td>
                          </tr>
                        ))
                      ) : (
                        <tr><td colSpan={5} className="p-8 text-center text-slate-500">لا يوجد موظفين</td></tr>
                      )}
                    </tbody>
                  </table>
                </div>
              </div>
            )}

            {/* Projects View */}
            {activeModule === "projects" && (
              <div className="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                {isLoadingProjects ? (
                  <div className="col-span-full p-8 text-center text-slate-500">جاري التحميل...</div>
                ) : projects && projects.length > 0 ? (
                  projects.map(project => (
                    <div key={project.id} className="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                      <div className="flex justify-between items-start mb-4">
                        <div className="w-12 h-12 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center shrink-0">
                          <Building2 className="w-6 h-6" />
                        </div>
                        <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                          {project.status === 'in_progress' ? 'قيد التنفيذ' : project.status}
                        </span>
                      </div>
                      <h3 className="font-bold text-slate-900 text-lg mb-1">{project.name}</h3>
                      <div className="text-sm text-slate-500 mb-4">{project.clientName}</div>
                      
                      <div className="space-y-3">
                        <div className="flex justify-between text-sm">
                          <span className="text-slate-500">المرحلة</span>
                          <span className="font-semibold text-slate-700">{project.stage}</span>
                        </div>
                        <div className="flex justify-between text-sm">
                          <span className="text-slate-500">الموقع</span>
                          <span className="font-semibold text-slate-700">{project.location}</span>
                        </div>
                        <div className="flex justify-between text-sm">
                          <span className="text-slate-500">الانتهاء المتوقع</span>
                          <span className="font-semibold text-slate-700">{format(new Date(project.endsAt), "MMM yyyy", { locale: ar })}</span>
                        </div>
                        
                        <div className="pt-3 border-t border-slate-100">
                          <div className="flex justify-between text-xs mb-1.5">
                            <span className="font-medium text-slate-700">نسبة الإنجاز</span>
                            <span className="font-bold text-slate-900">{project.progress}%</span>
                          </div>
                          <div className="w-full bg-slate-100 rounded-full h-2">
                            <div 
                              className={`h-2 rounded-full ${project.progress > 80 ? 'bg-emerald-500' : project.progress > 40 ? 'bg-amber-500' : 'bg-blue-500'}`} 
                              style={{ width: `${project.progress}%` }}
                            ></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  ))
                ) : (
                  <div className="col-span-full p-8 text-center text-slate-500">لا توجد مشاريع مضافة</div>
                )}
              </div>
            )}

            {/* Contracts View */}
            {activeModule === "contracts" && (
              <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div className="p-6 border-b border-slate-100 flex justify-between items-center">
                  <h3 className="text-lg font-bold text-slate-900">العقود المبرمة</h3>
                </div>
                <div className="overflow-x-auto">
                  <table className="w-full text-right">
                    <thead className="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm">
                      <tr>
                        <th className="px-6 py-4 font-semibold">رقم العقد</th>
                        <th className="px-6 py-4 font-semibold">المشروع</th>
                        <th className="px-6 py-4 font-semibold">العميل</th>
                        <th className="px-6 py-4 font-semibold">القيمة</th>
                        <th className="px-6 py-4 font-semibold">المدفوع</th>
                        <th className="px-6 py-4 font-semibold">الحالة</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                      {isLoadingContracts ? (
                        <tr><td colSpan={6} className="p-8 text-center text-slate-500">جاري التحميل...</td></tr>
                      ) : contracts && contracts.length > 0 ? (
                        contracts.map((contract) => (
                          <tr key={contract.id} className="hover:bg-slate-50/50 transition-colors">
                            <td className="px-6 py-4 font-medium text-slate-900" dir="ltr">{contract.code}</td>
                            <td className="px-6 py-4 text-slate-700 font-semibold">{contract.projectName}</td>
                            <td className="px-6 py-4 text-slate-600">{contract.clientName}</td>
                            <td className="px-6 py-4 text-slate-900 font-medium" dir="ltr">{contract.value.toLocaleString()} ر.س</td>
                            <td className="px-6 py-4 text-emerald-600 font-medium" dir="ltr">{contract.paidAmount.toLocaleString()} ر.س</td>
                            <td className="px-6 py-4">
                              <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold ${
                                contract.status === 'active' || contract.status === 'ساري' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'
                              }`}>
                                {contract.status === 'active' ? 'ساري' : contract.status}
                              </span>
                            </td>
                          </tr>
                        ))
                      ) : (
                        <tr><td colSpan={6} className="p-8 text-center text-slate-500">لا يوجد عقود</td></tr>
                      )}
                    </tbody>
                  </table>
                </div>
              </div>
            )}

            {/* Other modules fallback */}
            {!["dashboard", "employees", "projects", "contracts"].includes(activeModule) && (
              <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
                <div className="w-20 h-20 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                  {modules.find(m => m.id === activeModule)?.icon}
                </div>
                <h3 className="text-xl font-bold text-slate-900 mb-2">وحدة {modules.find(m => m.id === activeModule)?.label}</h3>
                <p className="text-slate-500 max-w-md mx-auto">هذه الوحدة مخصصة لإدارة العمليات المرتبطة بها في النظام الموحد. سيتم تفعيل الواجهات التفصيلية بناءً على الصلاحيات.</p>
                <button className="mt-6 bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-lg font-medium transition-all shadow-md">
                  إضافة سجل جديد
                </button>
              </div>
            )}

          </div>
        </div>
      </main>
      
    </div>
  );
}
