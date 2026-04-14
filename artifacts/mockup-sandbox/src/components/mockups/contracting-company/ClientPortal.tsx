import React from "react";
import { Building2, LogOut, Bell, FileText, CheckCircle2, Circle, Clock, Receipt, Image as ImageIcon, MapPin, ChevronLeft, ArrowLeft, Download, MessageSquare } from "lucide-react";

export function ClientPortal() {
  return (
    <div dir="rtl" className="min-h-screen bg-[#F8FAFC] font-sans text-slate-900 selection:bg-amber-500 selection:text-slate-900">
      
      {/* Top Navbar */}
      <nav className="bg-slate-900 text-white sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center gap-3">
              <div className="w-8 h-8 bg-amber-500 rounded flex items-center justify-center">
                <Building2 className="text-slate-900 w-5 h-5" />
              </div>
              <span className="font-bold text-xl hidden sm:block">أركان<span className="text-amber-500">البناء</span></span>
            </div>
            
            <div className="flex items-center gap-6">
              <div className="flex items-center gap-2 text-sm text-slate-300 border-l border-slate-700 pl-6 hidden md:flex">
                <span>شركة الراجحي العقارية</span>
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              </div>
              <button className="relative text-slate-300 hover:text-white transition-colors">
                <Bell className="w-5 h-5" />
                <span className="absolute -top-1 -right-1 w-2.5 h-2.5 bg-amber-500 rounded-full border-2 border-slate-900"></span>
              </button>
              <button className="text-slate-300 hover:text-white transition-colors flex items-center gap-2 text-sm font-medium">
                <span className="hidden sm:block">تسجيل خروج</span>
                <LogOut className="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </nav>

      {/* Main Content Area */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {/* Project Header */}
        <div className="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-slate-200 mb-8 relative overflow-hidden">
          <div className="absolute top-0 right-0 w-64 h-64 bg-amber-50 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 z-0"></div>
          
          <div className="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
              <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-semibold mb-4">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                قيد التنفيذ - متوافق مع الجدول الزمني
              </div>
              <h1 className="text-3xl md:text-4xl font-bold text-slate-900 mb-2">مجمع فيلات الياسمين السكني</h1>
              <div className="flex items-center gap-4 text-slate-500 text-sm">
                <div className="flex items-center gap-1">
                  <MapPin className="w-4 h-4" />
                  حي الياسمين، الرياض
                </div>
                <div className="flex items-center gap-1">
                  <Clock className="w-4 h-4" />
                  المتوقع للتسليم: ديسمبر 2024
                </div>
              </div>
            </div>
            
            <div className="flex gap-3">
              <button className="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg font-medium transition-all flex items-center gap-2 text-sm shadow-sm">
                <MessageSquare className="w-4 h-4" />
                تواصل مع المهندس
              </button>
              <button className="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-lg font-medium transition-all shadow-md flex items-center gap-2 text-sm">
                طلب تعديل/إضافة
              </button>
            </div>
          </div>
        </div>

        <div className="grid lg:grid-cols-3 gap-8">
          
          {/* Left Column (Progress & Updates) */}
          <div className="lg:col-span-2 space-y-8">
            
            {/* Progress Card */}
            <div className="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-slate-200">
              <div className="flex justify-between items-end mb-6">
                <div>
                  <h2 className="text-lg font-bold text-slate-900">نسبة الإنجاز الكلية</h2>
                  <p className="text-sm text-slate-500 mt-1">يتم تحديث النسبة أسبوعياً بناءً على تقارير الموقع</p>
                </div>
                <div className="text-4xl font-bold text-slate-900">65%</div>
              </div>
              
              <div className="w-full bg-slate-100 rounded-full h-3 mb-8">
                <div className="bg-amber-500 h-3 rounded-full relative" style={{ width: "65%" }}>
                  <div className="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-1/2 w-5 h-5 bg-white border-4 border-amber-500 rounded-full shadow"></div>
                </div>
              </div>
              
              <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                {[
                  { phase: "الحفر والأساسات", status: "completed", percent: "100%" },
                  { phase: "الهيكل العظمي", status: "completed", percent: "100%" },
                  { phase: "التشطيبات الداخلية", status: "in-progress", percent: "40%" },
                  { phase: "الواجهات والموقع العام", status: "pending", percent: "0%" }
                ].map((phase, i) => (
                  <div key={i} className="text-center">
                    <div className={`mx-auto w-10 h-10 rounded-full flex items-center justify-center mb-3 border-2 ${
                      phase.status === 'completed' ? 'bg-emerald-50 border-emerald-500 text-emerald-600' :
                      phase.status === 'in-progress' ? 'bg-amber-50 border-amber-500 text-amber-600' :
                      'bg-slate-50 border-slate-200 text-slate-300'
                    }`}>
                      {phase.status === 'completed' ? <CheckCircle2 className="w-5 h-5" /> : 
                       phase.status === 'in-progress' ? <div className="w-2 h-2 rounded-full bg-amber-500 animate-ping"></div> :
                       <Circle className="w-5 h-5" />}
                    </div>
                    <div className={`text-sm font-semibold mb-1 ${
                      phase.status === 'pending' ? 'text-slate-400' : 'text-slate-800'
                    }`}>{phase.phase}</div>
                    <div className="text-xs text-slate-500 font-medium" dir="ltr">{phase.percent}</div>
                  </div>
                ))}
              </div>
            </div>

            {/* Site Updates */}
            <div className="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
              <div className="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div className="flex items-center gap-2">
                  <ImageIcon className="w-5 h-5 text-slate-500" />
                  <h2 className="text-lg font-bold text-slate-900">تحديثات الموقع الأخيرة</h2>
                </div>
                <button className="text-sm font-medium text-amber-600 hover:text-amber-700">عرض الأرشيف</button>
              </div>
              
              <div className="p-6">
                <div className="mb-6 relative">
                  <div className="absolute right-6 top-10 bottom-4 w-0.5 bg-slate-100"></div>
                  
                  {/* Update Item 1 */}
                  <div className="relative pl-0 pr-12 mb-8">
                    <div className="absolute right-0 top-0 w-12 h-12 bg-white rounded-full border-4 border-slate-50 flex items-center justify-center z-10 shadow-sm">
                      <div className="w-3 h-3 bg-amber-500 rounded-full"></div>
                    </div>
                    <div className="bg-white border border-slate-100 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                      <div className="flex justify-between items-start mb-3">
                        <h3 className="font-bold text-slate-900">الانتهاء من صب سقف الدور الأول</h3>
                        <span className="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded">15 أكتوبر 2023</span>
                      </div>
                      <p className="text-sm text-slate-600 mb-4 leading-relaxed">
                        تم بحمد الله الانتهاء من أعمال صب الخرسانة لسقف الدور الأول لجميع الفيلات في المربع أ والمربع ب، وجاري حالياً أعمال المعالجة والمتابعة.
                      </p>
                      <div className="flex gap-3 overflow-x-auto pb-2">
                        <img src="/__mockup/images/contracting-project-1.png" className="w-32 h-24 object-cover rounded-lg border border-slate-200 flex-shrink-0" alt="Site update" />
                        <img src="/__mockup/images/contracting-project-2.png" className="w-32 h-24 object-cover rounded-lg border border-slate-200 flex-shrink-0 opacity-80 mix-blend-multiply" alt="Site update" />
                      </div>
                    </div>
                  </div>
                  
                  {/* Update Item 2 */}
                  <div className="relative pl-0 pr-12">
                    <div className="absolute right-0 top-0 w-12 h-12 bg-white rounded-full border-4 border-slate-50 flex items-center justify-center z-10 shadow-sm">
                      <div className="w-3 h-3 bg-emerald-500 rounded-full"></div>
                    </div>
                    <div className="bg-white border border-slate-100 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                      <div className="flex justify-between items-start mb-3">
                        <h3 className="font-bold text-slate-900">اعتماد عينات الرخام</h3>
                        <span className="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded">10 أكتوبر 2023</span>
                      </div>
                      <p className="text-sm text-slate-600 leading-relaxed">
                        تم توفير عينات رخام الأرضيات للصالات الرئيسية (نوع كرارا إيطالي درجة أولى). يرجى زيارة الموقع لمعاينتها على الطبيعة.
                      </p>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
            
          </div>

          {/* Right Column (Documents & Financials) */}
          <div className="space-y-8">
            
            {/* Financial Summary */}
            <div className="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
              <div className="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div className="flex items-center gap-2">
                  <Receipt className="w-5 h-5 text-slate-500" />
                  <h2 className="text-lg font-bold text-slate-900">المدفوعات والفواتير</h2>
                </div>
              </div>
              <div className="p-6">
                <div className="bg-slate-900 text-white rounded-xl p-5 mb-6 shadow-inner relative overflow-hidden">
                  <div className="absolute -left-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
                  <div className="text-sm text-slate-400 mb-1">إجمالي المدفوعات حتى الآن</div>
                  <div className="text-3xl font-bold mb-4 font-sans tracking-tight" dir="ltr">2,450,000 <span className="text-base text-slate-400 mr-1">SAR</span></div>
                  
                  <div className="border-t border-slate-700/50 pt-4 flex justify-between text-sm">
                    <span className="text-slate-400">الدفعة القادمة المستحقة:</span>
                    <span className="font-semibold text-amber-400" dir="ltr">350,000 SAR</span>
                  </div>
                </div>
                
                <div className="space-y-3">
                  <div className="flex justify-between items-center p-3 rounded-lg border border-emerald-100 bg-emerald-50/30">
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <CheckCircle2 className="w-4 h-4" />
                      </div>
                      <div>
                        <div className="font-semibold text-sm text-slate-900">الدفعة الثالثة (انتهاء الهيكل)</div>
                        <div className="text-xs text-slate-500">تم السداد في 1 سبتمبر</div>
                      </div>
                    </div>
                    <a href="#" className="text-slate-400 hover:text-slate-700">
                      <Download className="w-4 h-4" />
                    </a>
                  </div>
                  
                  <div className="flex justify-between items-center p-3 rounded-lg border border-slate-200 hover:border-slate-300 transition-colors cursor-pointer group">
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center shrink-0 group-hover:bg-slate-200">
                        <Receipt className="w-4 h-4" />
                      </div>
                      <div>
                        <div className="font-semibold text-sm text-slate-900">الدفعة الرابعة (التشطيبات)</div>
                        <div className="text-xs text-amber-600 font-medium mt-0.5">تستحق في 1 نوفمبر</div>
                      </div>
                    </div>
                    <ChevronLeft className="w-4 h-4 text-slate-400 group-hover:text-slate-700" />
                  </div>
                </div>
                
                <button className="w-full mt-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                  عرض الكشف المالي كامل
                </button>
              </div>
            </div>

            {/* Important Documents */}
            <div className="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
              <div className="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div className="flex items-center gap-2">
                  <FileText className="w-5 h-5 text-slate-500" />
                  <h2 className="text-lg font-bold text-slate-900">الوثائق والمخططات</h2>
                </div>
              </div>
              <div className="p-2">
                {[
                  { name: "العقد الرئيسي المعتمد", date: "يناير 2023", size: "2.4 MB" },
                  { name: "المخططات المعمارية النهائية", date: "مارس 2023", size: "15.8 MB" },
                  { name: "رخصة البناء وتصاريح البلدية", date: "فبراير 2023", size: "1.2 MB" },
                  { name: "جدول الكميات والمواصفات", date: "يناير 2023", size: "3.5 MB" }
                ].map((doc, i) => (
                  <a key={i} href="#" className="flex items-center justify-between p-4 rounded-xl hover:bg-slate-50 group transition-colors">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <FileText className="w-5 h-5" />
                      </div>
                      <div>
                        <div className="font-semibold text-sm text-slate-900 group-hover:text-amber-600 transition-colors">{doc.name}</div>
                        <div className="text-xs text-slate-500 mt-0.5">{doc.date} • {doc.size}</div>
                      </div>
                    </div>
                    <div className="text-slate-300 group-hover:text-slate-700 transition-colors bg-white p-1.5 rounded-md border border-transparent group-hover:border-slate-200 shadow-sm opacity-0 group-hover:opacity-100">
                      <Download className="w-4 h-4" />
                    </div>
                  </a>
                ))}
              </div>
              <div className="p-4 border-t border-slate-100 bg-slate-50">
                <button className="text-sm font-medium text-amber-600 hover:text-amber-700 w-full text-center">
                  الانتقال إلى الأرشيف المركزي
                </button>
              </div>
            </div>
            
          </div>
          
        </div>
      </div>
    </div>
  );
}
