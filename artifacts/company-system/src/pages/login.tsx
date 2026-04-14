import React, { useEffect, useState } from "react";
import { useLocation } from "wouter";
import { Building2, ArrowLeft, Mail, Lock, User, CheckCircle2, Shield } from "lucide-react";

export function Login() {
  const [, setLocation] = useLocation();
  const [role, setRole] = useState<"client" | "employee" | "admin">("client");
  const [email, setEmail] = useState("client@example.com");
  const [password, setPassword] = useState("123456");
  const [error, setError] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    setEmail(role === "admin" ? "admin@arkan-build.com" : role === "employee" ? "employee@arkan-build.com" : "client@example.com");
  }, [role]);

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setIsSubmitting(true);

    try {
      const response = await fetch(`${import.meta.env.BASE_URL}api/company/auth/login`, {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password, role }),
      });
      const payload = await response.json();
      if (!response.ok) {
        throw new Error(payload.error ?? "تعذر تسجيل الدخول");
      }

      localStorage.setItem("userRole", payload.user.role);
      localStorage.setItem("userName", payload.user.name);

      if (payload.user.role === "admin") {
        setLocation("/admin");
      } else if (payload.user.role === "employee") {
        setLocation("/employee");
      } else {
        setLocation("/client");
      }
    } catch (loginError) {
      setError(loginError instanceof Error ? loginError.message : "تعذر تسجيل الدخول");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div dir="rtl" className="min-h-screen bg-slate-50 font-sans text-slate-900 selection:bg-amber-500 selection:text-slate-900 flex flex-col md:flex-row">
      
      {/* Visual Side */}
      <div className="hidden md:flex w-1/2 bg-slate-900 relative overflow-hidden flex-col justify-between p-12">
        <div className="absolute inset-0 z-0">
          <div className="w-full h-full bg-slate-800 opacity-30 mix-blend-overlay grayscale flex items-center justify-center">
             <div className="w-full h-full bg-gradient-to-tr from-slate-800 to-slate-950"></div>
          </div>
          <div className="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent" />
        </div>
        
        <div className="relative z-10">
          <div className="flex items-center gap-2 mb-12">
            <div className="w-12 h-12 bg-amber-500 rounded flex items-center justify-center shadow-lg">
              <Building2 className="text-slate-900 w-7 h-7" />
            </div>
            <span className="font-bold text-3xl tracking-tight text-white">أركان<span className="text-amber-500">البناء</span></span>
          </div>
        </div>

        <div className="relative z-10 max-w-md">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-400 text-sm font-semibold mb-6">
            <span className="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            بوابة الدخول الموحدة
          </div>
          <h1 className="text-4xl font-bold text-white mb-6 leading-tight">
            نحن نبني المستقبل، <br/>وأنت تتابعه لحظة بلحظة.
          </h1>
          <p className="text-slate-400 text-lg leading-relaxed mb-8">
            قم بتسجيل الدخول للوصول إلى لوحة التحكم الخاصة بك ومتابعة كافة تفاصيل المشاريع والوثائق والموافقات بكل شفافية وأمان.
          </p>
          
          <div className="flex gap-4">
            <div className="flex items-center gap-2 text-slate-300">
              <CheckCircle2 className="w-5 h-5 text-amber-500" />
              <span>تحديثات فورية</span>
            </div>
            <div className="flex items-center gap-2 text-slate-300">
              <CheckCircle2 className="w-5 h-5 text-amber-500" />
              <span>أمان عالي</span>
            </div>
          </div>
        </div>
      </div>

      {/* Login Form Side */}
      <div className="w-full md:w-1/2 flex items-center justify-center p-8 md:p-12 relative bg-white">
        <div className="w-full max-w-md">
          
          <div className="md:hidden flex items-center gap-2 mb-12 justify-center">
            <div className="w-10 h-10 bg-amber-500 rounded flex items-center justify-center">
              <Building2 className="text-slate-900 w-6 h-6" />
            </div>
            <span className="font-bold text-2xl tracking-tight text-slate-900">أركان<span className="text-amber-600">البناء</span></span>
          </div>

          <div className="mb-10 text-center md:text-right">
            <h2 className="text-3xl font-bold text-slate-900 mb-3">مرحباً بك مجدداً</h2>
            <p className="text-slate-500">الرجاء إدخال بيانات الاعتماد الخاصة بك للدخول.</p>
          </div>

          {/* Role Toggle */}
          <div className="flex p-1 bg-slate-100 rounded-lg mb-8">
            <button 
              onClick={() => setRole("client")}
              data-testid="role-client"
              className={`flex-1 py-2.5 text-sm font-semibold rounded-md transition-all flex items-center justify-center gap-2 ${
                role === "client" 
                  ? "bg-white text-amber-600 shadow-sm border border-slate-200/60" 
                  : "text-slate-500 hover:text-slate-700"
              }`}
            >
              <User className="w-4 h-4" />
              بوابة العملاء
            </button>
            <button 
              onClick={() => setRole("employee")}
              data-testid="role-employee"
              className={`flex-1 py-2.5 text-sm font-semibold rounded-md transition-all flex items-center justify-center gap-2 ${
                role === "employee" 
                  ? "bg-slate-900 text-white shadow-sm" 
                  : "text-slate-500 hover:text-slate-700"
              }`}
            >
              <Building2 className="w-4 h-4" />
              بوابة الموظفين
            </button>
            <button 
              onClick={() => setRole("admin")}
              data-testid="role-admin"
              className={`flex-1 py-2.5 text-sm font-semibold rounded-md transition-all flex items-center justify-center gap-2 ${
                role === "admin" 
                  ? "bg-slate-900 text-white shadow-sm" 
                  : "text-slate-500 hover:text-slate-700"
              }`}
            >
              <Shield className="w-4 h-4" />
              الإدارة
            </button>
          </div>

          <form className="space-y-5" onSubmit={handleLogin}>
            <div className="space-y-2">
              <label className="text-sm font-semibold text-slate-700">
                {role === "client" ? "البريد الإلكتروني أو رقم الهاتف" : "البريد الإلكتروني الوظيفي"}
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                  <Mail className="w-5 h-5 text-slate-400" />
                </div>
                <input 
                  type="text" 
                  dir="ltr"
                  value={email}
                  onChange={(event) => setEmail(event.target.value)}
                  className="w-full bg-slate-50 border border-slate-200 text-left rounded-lg pl-4 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all" 
                  placeholder={role === "client" ? "client@example.com" : role === "admin" ? "admin@arkan-build.com" : "employee@arkan-build.com"} 
                  data-testid="input-email"
                />
              </div>
            </div>

            <div className="space-y-2">
              <div className="flex justify-between items-center">
                <label className="text-sm font-semibold text-slate-700">كلمة المرور</label>
                <a href="#" className="text-sm font-medium text-amber-600 hover:text-amber-700">نسيت كلمة المرور؟</a>
              </div>
              <div className="relative">
                <div className="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                  <Lock className="w-5 h-5 text-slate-400" />
                </div>
                <input 
                  type="password" 
                  dir="ltr"
                  value={password}
                  onChange={(event) => setPassword(event.target.value)}
                  className="w-full bg-slate-50 border border-slate-200 text-left rounded-lg pl-4 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all" 
                  placeholder="••••••••" 
                  data-testid="input-password"
                />
              </div>
            </div>

            <div className="flex items-center pt-2">
              <input 
                id="remember" 
                type="checkbox" 
                className="w-4 h-4 text-amber-500 bg-slate-100 border-slate-300 rounded focus:ring-amber-500"
              />
              <label htmlFor="remember" className="mr-2 text-sm font-medium text-slate-600">
                تذكرني على هذا الجهاز
              </label>
            </div>

            {error && (
              <div className="rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {error}
              </div>
            )}

            <div className="rounded-lg border border-amber-100 bg-amber-50 px-4 py-3 text-sm text-slate-700">
              بيانات الدخول التجريبية مفعلة من قاعدة البيانات. كلمة المرور الافتراضية: <span className="font-bold" dir="ltr">123456</span>
            </div>

            <button type="submit" data-testid="button-submit-login" className={`w-full py-3.5 rounded-lg font-bold text-lg transition-all shadow-md mt-6 flex items-center justify-center gap-2 ${
              role === "client"
                ? "bg-amber-500 hover:bg-amber-400 text-slate-900 shadow-[0_0_20px_rgba(245,158,11,0.2)]"
                : "bg-slate-900 hover:bg-slate-800 text-white shadow-[0_0_20px_rgba(15,23,42,0.2)]"
            }`} disabled={isSubmitting}>
              {isSubmitting ? "جاري التحقق..." : "تسجيل الدخول"}
              <ArrowLeft className="w-5 h-5" />
            </button>
          </form>

          {role === "client" && (
            <div className="mt-8 text-center text-sm text-slate-500">
              هل أنت عميل جديد؟ <a href="#" className="text-slate-900 font-semibold hover:text-amber-600 underline decoration-slate-300 underline-offset-4">تواصل معنا لبدء مشروعك</a>
            </div>
          )}
          
        </div>
      </div>
    </div>
  );
}
