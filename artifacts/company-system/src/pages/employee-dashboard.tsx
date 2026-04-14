import React, { useState } from "react";
import { useLocation } from "wouter";
import { Building2, Search, Bell, Menu, Home, FolderOpen, Calendar, Users, ClipboardCheck, Settings, TrendingUp, Clock, AlertTriangle, CheckCircle2, ChevronDown, FileText, LogOut } from "lucide-react";
import {
  getListApprovalsQueryKey,
  getListOperationalTasksQueryKey,
  useListProjects,
  useListApprovals,
  useListOperationalTasks,
  useUpdateApprovalStatus,
  useUpdateOperationalTaskStatus,
} from "@workspace/api-client-react";
import { useQueryClient } from "@tanstack/react-query";
import { useToast } from "@/hooks/use-toast";
import { format } from "date-fns";
import { ar } from "date-fns/locale";

export function EmployeeDashboard() {
  const [, setLocation] = useLocation();
  const [activeTab, setActiveTab] = useState("overview");
  const queryClient = useQueryClient();
  const { toast } = useToast();

  const { data: projects, isLoading: isLoadingProjects } = useListProjects();
  const { data: approvals, isLoading: isLoadingApprovals } = useListApprovals();
  const { data: tasks, isLoading: isLoadingTasks } = useListOperationalTasks();
  const updateApprovalStatus = useUpdateApprovalStatus({
    mutation: {
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: getListApprovalsQueryKey() });
        toast({ title: "تم تحديث الموافقة" });
      },
      onError: () => toast({ title: "تعذر تحديث الموافقة", variant: "destructive" }),
    },
  });
  const updateTaskStatus = useUpdateOperationalTaskStatus({
    mutation: {
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: getListOperationalTasksQueryKey() });
        toast({ title: "تم تحديث المهمة" });
      },
      onError: () => toast({ title: "تعذر تحديث المهمة", variant: "destructive" }),
    },
  });

  const handleLogout = () => {
    localStorage.removeItem("userRole");
    setLocation("/login");
  };

  const activeProjectsCount = projects?.filter(p => p.status === "in_progress" || p.status === "قيد التنفيذ" || p.status === "active").length || 12;
  const pendingApprovalsCount = approvals?.filter(a => a.status === "pending" || a.status === "معلق").length || 5;
  const pendingTasksCount = tasks?.filter(t => t.status === "pending" || t.status === "in_progress" || t.status === "قيد التنفيذ" || t.status === "معلق").length || 34;

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

        <div className="flex-1 py-6 px-3 space-y-1 overflow-y-auto">
          <div className="text-xs font-semibold text-slate-500 px-3 mb-4 mt-2">القائمة الرئيسية</div>
          
          <button className="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg bg-amber-500/10 text-amber-500 font-medium">
            <Home className="w-5 h-5" />
            لوحة القيادة
          </button>
          <button className="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
            <Building2 className="w-5 h-5" />
            المشاريع
            <span className="mr-auto bg-slate-800 text-xs py-0.5 px-2 rounded-full">{projects?.length || 12}</span>
          </button>
          <button className="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
            <ClipboardCheck className="w-5 h-5" />
            المهام والموافقات
            <span className="mr-auto bg-amber-500 text-slate-900 text-xs py-0.5 px-2 rounded-full font-bold">{pendingApprovalsCount + pendingTasksCount}</span>
          </button>
          <button className="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
            <FolderOpen className="w-5 h-5" />
            المخططات والوثائق
          </button>
          <button className="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
            <Calendar className="w-5 h-5" />
            الجدول الزمني
          </button>

          <div className="text-xs font-semibold text-slate-500 px-3 mb-4 mt-8">الإدارة</div>
          
          <button className="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
            <Users className="w-5 h-5" />
            فريق العمل
          </button>
          <button className="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
            <Settings className="w-5 h-5" />
            الإعدادات
          </button>
        </div>

        <div className="p-4 border-t border-slate-800">
          <div className="flex items-center gap-3 bg-slate-800/50 p-3 rounded-xl border border-slate-700/50 hover:bg-slate-800 transition-colors">
            <div className="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white border-2 border-slate-600">
              أ.ع
            </div>
            <div className="flex-1 min-w-0">
              <div className="text-sm font-semibold text-white truncate">أحمد عبدالله</div>
              <div className="text-xs text-slate-400 truncate">مهندس مشروع</div>
            </div>
            <button onClick={handleLogout} className="text-slate-500 hover:text-red-400 p-1" data-testid="button-logout">
              <LogOut className="w-4 h-4" />
            </button>
          </div>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        
        {/* Header */}
        <header className="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-8 shrink-0 z-10">
          <div className="flex items-center gap-4">
            <button className="lg:hidden text-slate-500 hover:text-slate-900">
              <Menu className="w-6 h-6" />
            </button>
            <h1 className="text-2xl font-bold text-slate-900 hidden sm:block">صباح الخير، المهندس أحمد</h1>
          </div>

          <div className="flex items-center gap-4 sm:gap-6">
            <div className="relative hidden md:block w-64">
              <div className="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <Search className="w-4 h-4 text-slate-400" />
              </div>
              <input 
                type="text" 
                className="w-full bg-slate-50 border border-slate-200 rounded-full pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all" 
                placeholder="ابحث في المشاريع، المهام..." 
              />
            </div>
            
            <button className="relative p-2 text-slate-400 hover:text-slate-900 transition-colors rounded-full hover:bg-slate-50">
              <Bell className="w-5 h-5" />
              <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
            </button>
            
            <button className="lg:hidden relative p-2 text-slate-400 hover:text-red-500 transition-colors rounded-full hover:bg-slate-50" onClick={handleLogout}>
              <LogOut className="w-5 h-5" />
            </button>
          </div>
        </header>

        {/* Scrollable Content */}
        <div className="flex-1 overflow-y-auto p-4 sm:p-8">
          <div className="max-w-7xl mx-auto space-y-8">
            
            {/* Quick Stats */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
              <div className="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden group hover:border-amber-200 transition-colors">
                <div className="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                <div className="relative flex justify-between items-start mb-4">
                  <div>
                    <div className="text-slate-500 text-sm font-medium mb-1">المشاريع النشطة</div>
                    <div className="text-3xl font-bold text-slate-900">{isLoadingProjects ? "..." : activeProjectsCount}</div>
                  </div>
                  <div className="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    <Building2 className="w-5 h-5" />
                  </div>
                </div>
                <div className="flex items-center gap-1 text-sm">
                  <TrendingUp className="w-4 h-4 text-emerald-500" />
                  <span className="text-emerald-500 font-medium">+2</span>
                  <span className="text-slate-400 ml-1">هذا الشهر</span>
                </div>
              </div>

              <div className="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden group hover:border-blue-200 transition-colors">
                <div className="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                <div className="relative flex justify-between items-start mb-4">
                  <div>
                    <div className="text-slate-500 text-sm font-medium mb-1">مهام قيد التنفيذ</div>
                    <div className="text-3xl font-bold text-slate-900">{isLoadingTasks ? "..." : pendingTasksCount}</div>
                  </div>
                  <div className="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <Clock className="w-5 h-5" />
                  </div>
                </div>
                <div className="flex items-center gap-1 text-sm">
                  <span className="text-slate-600 font-medium">8 مهام</span>
                  <span className="text-slate-400 ml-1">تستحق اليوم</span>
                </div>
              </div>

              <div className="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden group hover:border-red-200 transition-colors">
                <div className="absolute -right-4 -top-4 w-24 h-24 bg-red-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                <div className="relative flex justify-between items-start mb-4">
                  <div>
                    <div className="text-slate-500 text-sm font-medium mb-1">موافقات معلقة</div>
                    <div className="text-3xl font-bold text-slate-900">{isLoadingApprovals ? "..." : pendingApprovalsCount}</div>
                  </div>
                  <div className="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-red-600">
                    <AlertTriangle className="w-5 h-5" />
                  </div>
                </div>
                <div className="flex items-center gap-1 text-sm">
                  <span className="text-red-500 font-medium">تحتاج إجراء عاجل</span>
                </div>
              </div>

              <div className="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden group hover:border-emerald-200 transition-colors">
                <div className="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                <div className="relative flex justify-between items-start mb-4">
                  <div>
                    <div className="text-slate-500 text-sm font-medium mb-1">إنجاز الشهر</div>
                    <div className="text-3xl font-bold text-slate-900">86%</div>
                  </div>
                  <div className="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <CheckCircle2 className="w-5 h-5" />
                  </div>
                </div>
                <div className="w-full bg-slate-100 rounded-full h-1.5 mt-2">
                  <div className="bg-emerald-500 h-1.5 rounded-full" style={{ width: "86%" }}></div>
                </div>
              </div>
            </div>

            <div className="grid lg:grid-cols-3 gap-8">
              
              {/* Priority Projects */}
              <div className="lg:col-span-2 space-y-6">
                <div className="flex items-center justify-between">
                  <h2 className="text-xl font-bold text-slate-900">مشاريع ذات أولوية</h2>
                  <button className="text-sm font-medium text-amber-600 hover:text-amber-700">عرض الكل</button>
                </div>
                
                <div className="space-y-4">
                  {isLoadingProjects ? (
                    <div className="p-8 text-center text-slate-500">جاري تحميل المشاريع...</div>
                  ) : projects && projects.length > 0 ? (
                    projects.slice(0, 3).map((project, i) => (
                      <div key={i} className="bg-white p-5 rounded-xl border border-slate-200 hover:border-amber-300 transition-all shadow-sm group cursor-pointer" data-testid={`employee-project-${i}`}>
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                          <div className="flex items-start gap-4">
                            <div className="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center shrink-0">
                              <Building2 className="w-6 h-6 text-slate-400 group-hover:text-amber-500 transition-colors" />
                            </div>
                            <div>
                              <h3 className="font-bold text-slate-900 text-lg">{project.name}</h3>
                              <div className="text-sm text-slate-500">{project.clientName}</div>
                            </div>
                          </div>
                          <div className="flex flex-col sm:items-end">
                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 mb-1">
                              {project.status}
                            </span>
                            <span className="text-xs text-slate-400">التسليم: {format(new Date(project.endsAt), "MMMM yyyy", { locale: ar })}</span>
                          </div>
                        </div>
                        
                        <div>
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
                    ))
                  ) : (
                    <div className="p-8 text-center text-slate-500 bg-white rounded-xl border border-slate-200">لا توجد مشاريع حالياً</div>
                  )}
                </div>
              </div>

              {/* Action Items */}
              <div className="space-y-6">
                <h2 className="text-xl font-bold text-slate-900">إجراءات مطلوبة</h2>
                
                <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[calc(100%-2rem)] min-h-[400px]">
                  <div className="flex border-b border-slate-100">
                    <button 
                      className={`flex-1 py-3 text-sm font-semibold border-b-2 transition-colors ${activeTab === 'overview' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-700'}`}
                      onClick={() => setActiveTab('overview')}
                      data-testid="tab-approvals"
                    >
                      موافقات ({pendingApprovalsCount})
                    </button>
                    <button 
                      className={`flex-1 py-3 text-sm font-semibold border-b-2 transition-colors ${activeTab === 'tasks' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-700'}`}
                      onClick={() => setActiveTab('tasks')}
                      data-testid="tab-tasks"
                    >
                      مهام اليوم ({pendingTasksCount})
                    </button>
                  </div>
                  
                  <div className="p-4 flex-1 overflow-y-auto space-y-3">
                    {activeTab === 'overview' ? (
                      isLoadingApprovals ? (
                        <div className="text-center text-sm text-slate-500 py-8">جاري التحميل...</div>
                      ) : approvals && approvals.length > 0 ? (
                        approvals.slice(0, 4).map((item, i) => (
                          <div key={i} className="flex gap-3 p-3 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors cursor-pointer group" data-testid={`action-item-${i}`}>
                            <div className="w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-amber-100 text-amber-600">
                              <CheckCircle2 className="w-4 h-4" />
                            </div>
                            <div className="flex-1">
                              <div className="font-semibold text-sm text-slate-900 group-hover:text-amber-600 transition-colors">{item.title}</div>
                              <div className="flex items-center gap-2 mt-1">
                                <span className="text-xs text-slate-500">{item.projectName}</span>
                                <span className="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span className="text-xs text-slate-400">{format(new Date(item.createdAt), "dd MMM yyyy", { locale: ar })}</span>
                              </div>
                            </div>
                            {item.status === "pending" && (
                              <button
                                className="self-center shrink-0 rounded-md bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition-colors"
                                onClick={(event) => {
                                  event.stopPropagation();
                                  updateApprovalStatus.mutate({ id: item.id, data: { status: "approved" } });
                                }}
                                disabled={updateApprovalStatus.isPending}
                                data-testid={`approve-${item.id}`}
                              >
                                اعتماد
                              </button>
                            )}
                          </div>
                        ))
                      ) : (
                        <div className="text-center text-sm text-slate-500 py-8">لا توجد موافقات معلقة</div>
                      )
                    ) : (
                      isLoadingTasks ? (
                        <div className="text-center text-sm text-slate-500 py-8">جاري التحميل...</div>
                      ) : tasks && tasks.length > 0 ? (
                        tasks.slice(0, 4).map((task, i) => (
                          <div key={i} className="flex gap-3 p-3 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors cursor-pointer group" data-testid={`task-item-${i}`}>
                            <div className={`w-8 h-8 rounded-full flex items-center justify-center shrink-0 ${
                              task.priority === 'high' ? 'bg-red-100 text-red-600' :
                              task.priority === 'medium' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600'
                            }`}>
                              <FileText className="w-4 h-4" />
                            </div>
                            <div className="flex-1">
                              <div className="font-semibold text-sm text-slate-900 group-hover:text-amber-600 transition-colors">{task.title}</div>
                              <div className="flex items-center gap-2 mt-1">
                                <span className="text-xs text-slate-500">{task.projectName}</span>
                                <span className="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span className="text-xs text-slate-400">استحقاق: {format(new Date(task.dueAt), "dd MMM", { locale: ar })}</span>
                              </div>
                            </div>
                            {task.status !== "completed" && (
                              <button
                                className="self-center shrink-0 rounded-md bg-slate-900 px-3 py-1.5 text-xs font-bold text-white hover:bg-slate-800 transition-colors"
                                onClick={(event) => {
                                  event.stopPropagation();
                                  updateTaskStatus.mutate({ id: task.id, data: { status: "completed" } });
                                }}
                                disabled={updateTaskStatus.isPending}
                                data-testid={`complete-task-${task.id}`}
                              >
                                إنهاء
                              </button>
                            )}
                          </div>
                        ))
                      ) : (
                        <div className="text-center text-sm text-slate-500 py-8">لا توجد مهام</div>
                      )
                    )}
                  </div>
                  
                  <div className="p-3 border-t border-slate-100 bg-slate-50">
                    <button className="w-full py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-md hover:bg-slate-100 transition-colors">
                      فتح مركز المهام
                    </button>
                  </div>
                </div>
              </div>
              
            </div>
            
          </div>
        </div>
      </main>
      
    </div>
  );
}
