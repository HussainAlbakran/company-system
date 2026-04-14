import React, { useState } from "react";
import { Building2, Search, Bell, Menu, Home, FolderOpen, Calendar, Users, ClipboardCheck, Settings, TrendingUp, Clock, AlertTriangle, CheckCircle2, ChevronDown, FileText } from "lucide-react";

export function EmployeeDashboard() {
  const [activeTab, setActiveTab] = useState("overview");

  return (
    <div dir="rtl" className="min-h-screen bg-slate-50 font-sans text-slate-900 flex selection:bg-amber-500 selection:text-slate-900">
      
      {/* Sidebar */}
      <aside className="w-64 bg-slate-900 text-slate-300 hidden lg:flex flex-col border-l border-slate-800">
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
            <span className="mr-auto bg-slate-800 text-xs py-0.5 px-2 rounded-full">12</span>
          </button>
          <button className="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition-colors">
            <ClipboardCheck className="w-5 h-5" />
            المهام والموافقات
            <span className="mr-auto bg-amber-500 text-slate-900 text-xs py-0.5 px-2 rounded-full font-bold">5</span>
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
          <div className="flex items-center gap-3 bg-slate-800/50 p-3 rounded-xl border border-slate-700/50 cursor-pointer hover:bg-slate-800 transition-colors">
            <div className="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white border-2 border-slate-600">
              أ.ع
            </div>
            <div className="flex-1 min-w-0">
              <div className="text-sm font-semibold text-white truncate">أحمد عبدالله</div>
              <div className="text-xs text-slate-400 truncate">مهندس مشروع</div>
            </div>
            <ChevronDown className="w-4 h-4 text-slate-500" />
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
                    <div className="text-3xl font-bold text-slate-900">12</div>
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
                    <div className="text-3xl font-bold text-slate-900">34</div>
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
                    <div className="text-3xl font-bold text-slate-900">5</div>
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
                  {[
                    { name: "مجمع فيلات الياسمين", client: "شركة الراجحي العقارية", progress: 65, status: "قيد التنفيذ", dueDate: "ديسمبر 2024" },
                    { name: "برج الأعمال الإداري", client: "مجموعة العليان", progress: 32, status: "مرحلة الأساسات", dueDate: "مارس 2025" },
                    { name: "ترميم قصر السلام", client: "عميل خاص", progress: 89, status: "التشطيبات النهائية", dueDate: "أكتوبر 2023" }
                  ].map((project, i) => (
                    <div key={i} className="bg-white p-5 rounded-xl border border-slate-200 hover:border-amber-300 transition-all shadow-sm group cursor-pointer">
                      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                        <div className="flex items-start gap-4">
                          <div className="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center shrink-0">
                            <Building2 className="w-6 h-6 text-slate-400 group-hover:text-amber-500 transition-colors" />
                          </div>
                          <div>
                            <h3 className="font-bold text-slate-900 text-lg">{project.name}</h3>
                            <div className="text-sm text-slate-500">{project.client}</div>
                          </div>
                        </div>
                        <div className="flex flex-col sm:items-end">
                          <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 mb-1">
                            {project.status}
                          </span>
                          <span className="text-xs text-slate-400">التسليم: {project.dueDate}</span>
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
                  ))}
                </div>
              </div>

              {/* Action Items */}
              <div className="space-y-6">
                <h2 className="text-xl font-bold text-slate-900">إجراءات مطلوبة</h2>
                
                <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[calc(100%-2rem)]">
                  <div className="flex border-b border-slate-100">
                    <button 
                      className={`flex-1 py-3 text-sm font-semibold border-b-2 transition-colors ${activeTab === 'overview' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-700'}`}
                      onClick={() => setActiveTab('overview')}
                    >
                      موافقات (5)
                    </button>
                    <button 
                      className={`flex-1 py-3 text-sm font-semibold border-b-2 transition-colors ${activeTab === 'tasks' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-700'}`}
                      onClick={() => setActiveTab('tasks')}
                    >
                      مهام اليوم (8)
                    </button>
                  </div>
                  
                  <div className="p-4 flex-1 overflow-y-auto space-y-3">
                    {[
                      { title: "اعتماد طلب توريد حديد", project: "برج الأعمال", time: "منذ ساعتين", type: "approval" },
                      { title: "مراجعة المخطط الإنشائي المعدل", project: "مجمع الياسمين", time: "منذ 4 ساعات", type: "review" },
                      { title: "فاتورة مقاول الباطن #1023", project: "ترميم قصر السلام", time: "أمس", type: "financial" },
                      { title: "طلب إجازة - المهندس خالد", project: "إدارة", time: "أمس", type: "hr" },
                    ].map((item, i) => (
                      <div key={i} className="flex gap-3 p-3 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors cursor-pointer group">
                        <div className={`w-8 h-8 rounded-full flex items-center justify-center shrink-0 ${
                          item.type === 'approval' ? 'bg-amber-100 text-amber-600' :
                          item.type === 'review' ? 'bg-blue-100 text-blue-600' :
                          item.type === 'financial' ? 'bg-emerald-100 text-emerald-600' : 'bg-purple-100 text-purple-600'
                        }`}>
                          {item.type === 'approval' ? <CheckCircle2 className="w-4 h-4" /> :
                           item.type === 'review' ? <FileText className="w-4 h-4" /> :
                           item.type === 'financial' ? <ClipboardCheck className="w-4 h-4" /> : <Users className="w-4 h-4" />}
                        </div>
                        <div>
                          <div className="font-semibold text-sm text-slate-900 group-hover:text-amber-600 transition-colors">{item.title}</div>
                          <div className="flex items-center gap-2 mt-1">
                            <span className="text-xs text-slate-500">{item.project}</span>
                            <span className="w-1 h-1 rounded-full bg-slate-300"></span>
                            <span className="text-xs text-slate-400">{item.time}</span>
                          </div>
                        </div>
                      </div>
                    ))}
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
