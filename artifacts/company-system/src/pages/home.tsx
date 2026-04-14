import React, { useState } from "react";
import { motion } from "framer-motion";
import { Link } from "wouter";
import { HardHat, Ruler, Building2, CheckCircle2, ChevronLeft, Phone, Mail, MapPin, ArrowLeft, Menu, X } from "lucide-react";

export function Home() {
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  const fadeIn = {
    initial: { opacity: 0, y: 20 },
    animate: { opacity: 1, y: 0 },
    transition: { duration: 0.6 }
  };

  const staggerContainer = {
    animate: {
      transition: {
        staggerChildren: 0.1
      }
    }
  };

  return (
    <div dir="rtl" className="min-h-screen bg-slate-50 font-sans text-slate-900 selection:bg-amber-500 selection:text-slate-900">
      {/* Navigation */}
      <nav className="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-20 items-center">
            <Link href="/" className="flex items-center gap-2 cursor-pointer" data-testid="link-home">
              <img src="/company-logo.png" alt="شركة التقدم للخرسانة الجاهزة" className="h-12 w-auto" />
            </Link>
            
            <div className="hidden md:flex items-center gap-8 font-medium">
              <a href="#services" className="text-slate-600 hover:text-amber-600 transition-colors" data-testid="link-services">خدماتنا</a>
              <a href="#about" className="text-slate-600 hover:text-amber-600 transition-colors" data-testid="link-about">من نحن</a>
              <a href="#projects" className="text-slate-600 hover:text-amber-600 transition-colors" data-testid="link-projects">مشاريعنا</a>
              <a href="#contact" className="text-slate-600 hover:text-amber-600 transition-colors" data-testid="link-contact">اتصل بنا</a>
            </div>

            <div className="hidden md:flex gap-4">
              <Link href="/login" className="text-slate-600 hover:text-amber-600 px-4 py-2.5 font-medium transition-colors flex items-center" data-testid="link-login">
                تسجيل الدخول
              </Link>
              <button className="bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded font-medium transition-all shadow-md hover:shadow-lg flex items-center gap-2" data-testid="button-quote">
                اطلب تسعيرة
                <ArrowLeft className="w-4 h-4" />
              </button>
            </div>

            <div className="md:hidden flex items-center">
              <button onClick={() => setIsMenuOpen(!isMenuOpen)} className="text-slate-600 hover:text-slate-900" data-testid="button-menu">
                {isMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
              </button>
            </div>
          </div>
        </div>
        
        {/* Mobile menu */}
        {isMenuOpen && (
          <div className="md:hidden bg-white border-b border-slate-200 px-4 pt-2 pb-4 space-y-1 shadow-lg">
            <a href="#services" className="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-amber-600 hover:bg-slate-50">خدماتنا</a>
            <a href="#about" className="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-amber-600 hover:bg-slate-50">من نحن</a>
            <a href="#projects" className="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-amber-600 hover:bg-slate-50">مشاريعنا</a>
            <a href="#contact" className="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-amber-600 hover:bg-slate-50">اتصل بنا</a>
            <Link href="/login" className="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-amber-600 hover:bg-slate-50">تسجيل الدخول</Link>
            <button className="w-full text-center mt-4 bg-amber-500 hover:bg-amber-600 text-slate-900 px-6 py-3 rounded font-bold transition-all shadow-md">
              اطلب تسعيرة
            </button>
          </div>
        )}
      </nav>

      {/* Hero Section */}
      <div className="relative pt-20 pb-20 lg:pt-32 lg:pb-28 overflow-hidden bg-slate-900 text-white">
        <div className="absolute inset-0 z-0">
          <div className="w-full h-full bg-slate-800 opacity-40 mix-blend-overlay flex items-center justify-center">
             <div className="w-full h-full bg-gradient-to-br from-slate-800 to-slate-950"></div>
          </div>
          <div className="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/90 to-transparent" />
        </div>
        
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <motion.div 
              initial="initial" 
              animate="animate" 
              variants={staggerContainer}
              className="max-w-2xl"
            >
              <motion.div variants={fadeIn} className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-400 text-sm font-semibold mb-6">
                <span className="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                رواد الخرسانة الجاهزة في المملكة
              </motion.div>
              <motion.h1 variants={fadeIn} className="text-5xl lg:text-7xl font-bold leading-tight mb-6 text-white">
                نبني المستقبل بأسس <span className="text-amber-500">راسخة</span>
              </motion.h1>
              <motion.p variants={fadeIn} className="text-lg text-slate-300 mb-8 leading-relaxed max-w-lg">
                شركة رائدة في تصنيع وتوريد الخرسانة الجاهزة والمنتجات الخرسانية عالية الجودة لمختلف المشاريع الإنشائية والقطاعات بجودة لا تضاهى والتزام تام.
              </motion.p>
              <motion.div variants={fadeIn} className="flex flex-col sm:flex-row gap-4">
                <button className="bg-amber-500 hover:bg-amber-400 text-slate-900 px-8 py-3.5 rounded font-bold text-lg transition-all shadow-[0_0_20px_rgba(245,158,11,0.3)] hover:shadow-[0_0_30px_rgba(245,158,11,0.5)] flex items-center justify-center gap-2" data-testid="button-start-project">
                  ابدأ مشروعك الآن
                  <ArrowLeft className="w-5 h-5" />
                </button>
                <button className="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-8 py-3.5 rounded font-medium text-lg transition-all flex items-center justify-center backdrop-blur-sm" data-testid="button-browse-projects">
                  تصفح مشاريعنا
                </button>
              </motion.div>
            </motion.div>
            
            <motion.div 
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.8, delay: 0.2 }}
              className="hidden lg:block relative"
            >
              <div className="absolute -inset-4 bg-amber-500/20 rounded-2xl blur-2xl"></div>
              <div className="relative border-r-8 border-b-8 border-amber-500 rounded-lg overflow-hidden bg-slate-800 aspect-[4/3] shadow-2xl flex items-center justify-center">
                 <div className="w-full h-full bg-gradient-to-tr from-slate-700 to-slate-600"></div>
              </div>
              
              {/* Floating Stat Card */}
              <div className="absolute -bottom-8 -left-8 bg-white text-slate-900 p-6 rounded-lg shadow-xl border border-slate-100 max-w-xs">
                <div className="flex items-center gap-4 mb-2">
                  <div className="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center text-amber-600">
                    <CheckCircle2 className="w-6 h-6" />
                  </div>
                  <div>
                    <div className="text-3xl font-bold">100%</div>
                    <div className="text-sm text-slate-600 font-medium">تسليم في الموعد</div>
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </div>

      {/* Stats Section */}
      <div className="bg-slate-900 border-t border-slate-800 pb-12 relative z-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8 -mt-8 relative bg-white rounded-xl shadow-xl p-8 border border-slate-100">
            {[
              { number: "+15", label: "عاماً من الخبرة" },
              { number: "+120", label: "مشروع منجز" },
              { number: "+50", label: "مهندس وخبير" },
              { number: "100%", label: "رضا العملاء" }
            ].map((stat, i) => (
              <div key={i} className="text-center" data-testid={`stat-${i}`}>
                <div className="text-4xl md:text-5xl font-bold text-slate-900 mb-2">{stat.number}</div>
                <div className="text-slate-500 font-medium">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Services Section */}
      <section id="services" className="py-24 bg-slate-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-3xl mx-auto mb-16">
            <h2 className="text-amber-600 font-bold tracking-wider uppercase mb-2">خدماتنا</h2>
            <h3 className="text-3xl md:text-4xl font-bold text-slate-900 mb-4">حلول هندسية متكاملة</h3>
            <p className="text-slate-600 text-lg">نقدم مجموعة واسعة من خدمات المقاولات التي تلبي كافة احتياجاتك وتتجاوز توقعاتك.</p>
          </div>

          <div className="grid md:grid-cols-3 gap-8">
            {[
              {
                icon: <Building2 className="w-8 h-8" />,
                title: "بناء الفيلات والقصور",
                desc: "تنفيذ أرقى التصاميم المعمارية السكنية بأعلى معايير الجودة والرفاهية التي تليق بك."
              },
              {
                icon: <HardHat className="w-8 h-8" />,
                title: "المشاريع التجارية",
                desc: "إنشاء مباني مكتبية ومجمعات تجارية بمواصفات عالمية تدعم نجاح أعمالك."
              },
              {
                icon: <Ruler className="w-8 h-8" />,
                title: "التشطيبات والترميم",
                desc: "تجديد المساحات وإعادة هيكلتها بأحدث المواد وأرقى اللمسات الديكورية."
              }
            ].map((service, i) => (
              <motion.div 
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.1, duration: 0.5 }}
                key={i} 
                className="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all border border-slate-100 group"
                data-testid={`service-${i}`}
              >
                <div className="h-48 overflow-hidden relative bg-slate-200 flex items-center justify-center">
                  <div className="absolute inset-0 bg-slate-900/20 group-hover:bg-transparent transition-all z-10"></div>
                  <div className="text-slate-400 w-full h-full bg-gradient-to-br from-slate-200 to-slate-300"></div>
                </div>
                <div className="p-8">
                  <div className="w-14 h-14 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center mb-6 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                    {service.icon}
                  </div>
                  <h4 className="text-xl font-bold text-slate-900 mb-3">{service.title}</h4>
                  <p className="text-slate-600 mb-6 leading-relaxed">{service.desc}</p>
                  <a href="#" className="inline-flex items-center gap-2 text-amber-600 font-semibold hover:text-amber-700 transition-colors">
                    اقرأ المزيد
                    <ChevronLeft className="w-4 h-4" />
                  </a>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Contact Section */}
      <section id="contact" className="py-24 bg-amber-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
            <div className="grid lg:grid-cols-5 h-full">
              
              {/* Contact Info */}
              <div className="lg:col-span-2 bg-slate-900 text-white p-10 md:p-12 relative overflow-hidden">
                <div className="absolute top-0 right-0 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                
                <h3 className="text-3xl font-bold mb-4 relative z-10">لنتحدث عن مشروعك</h3>
                <p className="text-slate-400 mb-12 relative z-10">نحن هنا للإجابة على استفساراتك وتقديم استشارة هندسية مبدئية مجانية لمشروعك القادم.</p>
                
                <div className="space-y-8 relative z-10">
                  <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-white/10 rounded flex items-center justify-center shrink-0">
                      <Phone className="w-5 h-5 text-amber-400" />
                    </div>
                    <div>
                      <div className="text-slate-400 text-sm mb-1">اتصل بنا</div>
                      <div className="text-xl font-medium" dir="ltr">+966 50 123 4567</div>
                    </div>
                  </div>
                  
                  <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-white/10 rounded flex items-center justify-center shrink-0">
                      <Mail className="w-5 h-5 text-amber-400" />
                    </div>
                    <div>
                      <div className="text-slate-400 text-sm mb-1">البريد الإلكتروني</div>
                      <div className="text-lg font-medium font-sans" dir="ltr">info@advance-precast.com</div>
                    </div>
                  </div>
                  
                  <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-white/10 rounded flex items-center justify-center shrink-0">
                      <MapPin className="w-5 h-5 text-amber-400" />
                    </div>
                    <div>
                      <div className="text-slate-400 text-sm mb-1">المقر الرئيسي</div>
                      <div className="text-lg font-medium leading-relaxed">طريق الملك فهد، حي الملقا<br/>الرياض، المملكة العربية السعودية</div>
                    </div>
                  </div>
                </div>
              </div>
              
              {/* Form */}
              <div className="lg:col-span-3 p-10 md:p-12">
                <h3 className="text-2xl font-bold text-slate-900 mb-8">اطلب تسعيرة مبدئية</h3>
                
                <form className="space-y-6" onSubmit={(e) => e.preventDefault()}>
                  <div className="grid sm:grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <label className="text-sm font-medium text-slate-700">الاسم الكامل</label>
                      <input type="text" className="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" placeholder="أدخل اسمك" />
                    </div>
                    <div className="space-y-2">
                      <label className="text-sm font-medium text-slate-700">رقم الجوال</label>
                      <input type="tel" className="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" placeholder="05x xxx xxxx" dir="ltr" />
                    </div>
                  </div>
                  
                  <div className="space-y-2">
                    <label className="text-sm font-medium text-slate-700">نوع المشروع</label>
                    <select className="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all appearance-none">
                      <option>فيلا سكنية</option>
                      <option>عمارة سكنية/تجارية</option>
                      <option>مبنى تجاري/مكتبي</option>
                      <option>تشطيب وترميم</option>
                      <option>أخرى</option>
                    </select>
                  </div>

                  <div className="space-y-2">
                    <label className="text-sm font-medium text-slate-700">تفاصيل المشروع (اختياري)</label>
                    <textarea className="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all min-h-[120px]" placeholder="اكتب تفاصيل إضافية حول مشروعك هنا..."></textarea>
                  </div>
                  
                  <button className="bg-amber-500 hover:bg-amber-400 text-slate-900 w-full py-4 rounded-lg font-bold text-lg transition-all shadow-md flex items-center justify-center gap-2" data-testid="button-submit-quote">
                    إرسال الطلب
                    <ArrowLeft className="w-5 h-5" />
                  </button>
                </form>
              </div>
              
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
