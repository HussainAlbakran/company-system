import React, { useState } from "react";
import { useLocation } from "wouter";
import { 
  Building2, Search, Bell, Menu, LayoutDashboard, FileText, Users, 
  Settings, FolderOpen, PieChart, ShieldCheck, Box, Factory, Wrench, 
  Car, MessageSquare, Briefcase, LogOut, ChevronLeft
} from "lucide-react";

export function AdminDashboard() {
  const [, setLocation] = useLocation();
  const [activeModule, setActiveModule] = useState("dashboard");

  const handleLogout = () => {
    localStorage.removeItem("userRole");
    setLocation("/login");
  };

  const modules = [
    { id: "dashboard", icon: <LayoutDashboard />, label: "لوحة التحكم" },
    { id: "reports", icon: <PieChart />, label: "التقارير" },
    { id: "users", icon: <ShieldCheck />, label: "المستخدمين والصلاحيات" },
    { id: "employees", icon: <Users />, label: "الموظفين والرواتب" },
    { id: "contracts", icon: <FileText />, label: "العقود والمقاولين" },
    { id: "projects", icon: <Briefcase />, label: "المشاريع الهندسية" },
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
            <div className="flex items-center gap-3 mb-6">
              <div className="w-10 h-10 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
                {modules.find(m => m.id === activeModule)?.icon}
              </div>
              <h2 className="text-2xl font-bold text-slate-900">{modules.find(m => m.id === activeModule)?.label}</h2>
            </div>

            {/* Dashboard Overview content - simplified for MVP */}
            {activeModule === "dashboard" && (
              <>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                  {[
                    { label: "إجمالي المشاريع", value: "48", desc: "12 نشط حالياً", color: "bg-blue-500" },
                    { label: "الموظفين", value: "342", desc: "98% نسبة الحضور", color: "bg-emerald-500" },
                    { label: "العقود السارية", value: "156", desc: "قيمة 45M ريال", color: "bg-amber-500" },
                    { label: "تنبيهات النظام", value: "14", desc: "تحتاج مراجعة", color: "bg-red-500" }
                  ].map((stat, i) => (
                    <div key={i} className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group">
                      <div className="relative z-10">
                        <div className="text-slate-500 text-sm font-medium mb-2">{stat.label}</div>
                        <div className="text-4xl font-bold text-slate-900 mb-2">{stat.value}</div>
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
                      {[
                        { text: "تم تسجيل عقد جديد مع شركة الراجحي", time: "قبل 10 دقائق", user: "قسم المبيعات" },
                        { text: "اعتماد طلب شراء مواد خام #4421", time: "قبل ساعة", user: "المدير المالي" },
                        { text: "تحديث حالة مشروع الياسمين إلى 65%", time: "قبل ساعتين", user: "المهندس أحمد" }
                      ].map((log, i) => (
                        <div key={i} className="flex gap-4 items-start pb-4 border-b border-slate-100 last:border-0 last:pb-0">
                          <div className="w-2 h-2 rounded-full bg-amber-500 mt-2 shrink-0"></div>
                          <div>
                            <div className="font-semibold text-slate-800 text-sm">{log.text}</div>
                            <div className="text-xs text-slate-500 mt-1">{log.time} • {log.user}</div>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>

                  <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 className="text-lg font-bold text-slate-900 mb-4">موافقات إدارية معلقة</h3>
                    <div className="space-y-3">
                      {[
                        { title: "صرف دفعة مقاول باطن", amount: "125,000 ريال", urgency: "عاجل" },
                        { title: "اعتماد تعيين مهندس معماري", amount: "راتب 15,000", urgency: "متوسط" },
                        { title: "تجديد رخصة مستودع الدمام", amount: "4,500 ريال", urgency: "عاجل" }
                      ].map((app, i) => (
                        <div key={i} className="flex items-center justify-between p-3 rounded-lg border border-slate-100 hover:border-amber-200 transition-colors bg-slate-50/50">
                          <div>
                            <div className="font-bold text-slate-800 text-sm mb-1">{app.title}</div>
                            <div className="text-xs font-medium text-slate-500">{app.amount}</div>
                          </div>
                          <div className="flex items-center gap-3">
                            <span className={`text-xs px-2 py-1 rounded font-semibold ${app.urgency === 'عاجل' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'}`}>
                              {app.urgency}
                            </span>
                            <button className="text-amber-600 hover:bg-amber-50 p-1.5 rounded">
                              <ChevronLeft className="w-5 h-5" />
                            </button>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              </>
            )}

            {activeModule !== "dashboard" && (
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
