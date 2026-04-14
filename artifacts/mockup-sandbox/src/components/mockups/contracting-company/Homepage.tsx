import React, { useState } from "react";
import { motion } from "framer-motion";
import { HardHat, Ruler, Building2, CheckCircle2, ChevronLeft, Phone, Mail, MapPin, ArrowLeft, Menu, X } from "lucide-react";

export function Homepage() {
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
            <div className="flex items-center gap-2">
              <div className="w-10 h-10 bg-amber-500 rounded flex items-center justify-center">
                <Building2 className="text-slate-900 w-6 h-6" />
              </div>
              <span className="font-bold text-2xl tracking-tight text-slate-900">أركان<span className="text-amber-600">البناء</span></span>
            </div>
            
            <div className="hidden md:flex items-center gap-8 font-medium">
              <a href="#services" className="text-slate-600 hover:text-amber-600 transition-colors">خدماتنا</a>
              <a href="#about" className="text-slate-600 hover:text-amber-600 transition-colors">من نحن</a>
              <a href="#projects" className="text-slate-600 hover:text-amber-600 transition-colors">مشاريعنا</a>
              <a href="#contact" className="text-slate-600 hover:text-amber-600 transition-colors">اتصل بنا</a>
            </div>

            <div className="hidden md:flex">
              <button className="bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded font-medium transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                اطلب تسعيرة
                <ArrowLeft className="w-4 h-4" />
              </button>
            </div>

            <div className="md:hidden flex items-center">
              <button onClick={() => setIsMenuOpen(!isMenuOpen)} className="text-slate-600 hover:text-slate-900">
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
            <button className="w-full text-center mt-4 bg-amber-500 hover:bg-amber-600 text-slate-900 px-6 py-3 rounded font-bold transition-all shadow-md">
              اطلب تسعيرة
            </button>
          </div>
        )}
      </nav>

      {/* Hero Section */}
      <div className="relative pt-20 pb-20 lg:pt-32 lg:pb-28 overflow-hidden bg-slate-900 text-white">
        <div className="absolute inset-0 z-0">
          <img 
            src="/__mockup/images/contracting-hero.png" 
            alt="Construction site" 
            className="w-full h-full object-cover opacity-40 mix-blend-overlay"
          />
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
                رواد المقاولات في المملكة
              </motion.div>
              <motion.h1 variants={fadeIn} className="text-5xl lg:text-7xl font-bold leading-tight mb-6 text-white">
                نبني المستقبل بأسس <span className="text-amber-500">راسخة</span>
              </motion.h1>
              <motion.p variants={fadeIn} className="text-lg text-slate-300 mb-8 leading-relaxed max-w-lg">
                شركة مقاولات رائدة في تقديم حلول البناء المتكاملة، من الفيلات السكنية الفاخرة إلى المشاريع التجارية، بجودة لا تضاهى والتزام تام بالمواعيد.
              </motion.p>
              <motion.div variants={fadeIn} className="flex flex-col sm:flex-row gap-4">
                <button className="bg-amber-500 hover:bg-amber-400 text-slate-900 px-8 py-3.5 rounded font-bold text-lg transition-all shadow-[0_0_20px_rgba(245,158,11,0.3)] hover:shadow-[0_0_30px_rgba(245,158,11,0.5)] flex items-center justify-center gap-2">
                  ابدأ مشروعك الآن
                  <ArrowLeft className="w-5 h-5" />
                </button>
                <button className="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-8 py-3.5 rounded font-medium text-lg transition-all flex items-center justify-center backdrop-blur-sm">
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
              <div className="relative border-r-8 border-b-8 border-amber-500 rounded-lg overflow-hidden bg-slate-800 aspect-[4/3] shadow-2xl">
                <img 
                  src="/__mockup/images/contracting-hero.png" 
                  alt="Modern Architecture" 
                  className="w-full h-full object-cover"
                />
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
              <div key={i} className="text-center">
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
                desc: "تنفيذ أرقى التصاميم المعمارية السكنية بأعلى معايير الجودة والرفاهية التي تليق بك.",
                image: "/__mockup/images/contracting-service-1.png"
              },
              {
                icon: <HardHat className="w-8 h-8" />,
                title: "المشاريع التجارية",
                desc: "إنشاء مباني مكتبية ومجمعات تجارية بمواصفات عالمية تدعم نجاح أعمالك.",
                image: "/__mockup/images/contracting-service-2.png"
              },
              {
                icon: <Ruler className="w-8 h-8" />,
                title: "التشطيبات والترميم",
                desc: "تجديد المساحات وإعادة هيكلتها بأحدث المواد وأرقى اللمسات الديكورية.",
                image: "/__mockup/images/contracting-service-3.png"
              }
            ].map((service, i) => (
              <motion.div 
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.1, duration: 0.5 }}
                key={i} 
                className="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all border border-slate-100 group"
              >
                <div className="h-48 overflow-hidden relative">
                  <div className="absolute inset-0 bg-slate-900/20 group-hover:bg-transparent transition-all z-10"></div>
                  <img src={service.image} alt={service.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
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

      {/* About Section */}
      <section id="about" className="py-24 bg-white overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-16 items-center">
            <motion.div 
              initial={{ opacity: 0, x: 20 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              className="relative"
            >
              <div className="relative z-10 rounded-lg overflow-hidden shadow-2xl border-4 border-white">
                <img src="/__mockup/images/contracting-about.png" alt="Engineer" className="w-full h-full object-cover aspect-square" />
              </div>
              <div className="absolute top-1/2 -right-8 -translate-y-1/2 w-32 h-32 bg-amber-500 rounded-full blur-3xl opacity-30 z-0"></div>
              
              {/* Experience Badge */}
              <div className="absolute -bottom-6 -right-6 bg-slate-900 text-white p-6 rounded-lg shadow-xl border border-slate-700 z-20">
                <div className="text-4xl font-bold text-amber-500 mb-1">+15</div>
                <div className="text-sm font-medium">عاماً من<br/>التميز المعماري</div>
              </div>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
            >
              <h2 className="text-amber-600 font-bold tracking-wider uppercase mb-2">لماذا تختارنا؟</h2>
              <h3 className="text-3xl md:text-4xl font-bold text-slate-900 mb-6 leading-tight">
                نحول رؤيتك إلى واقع ملموس بأعلى معايير الإتقان
              </h3>
              <p className="text-slate-600 text-lg mb-8 leading-relaxed">
                تأسست شركة أركان البناء على مبدأ الجودة والثقة. نحن نؤمن بأن كل مشروع هو بصمة غنية نتركها في المشهد العمراني، لذلك نحرص على تقديم أفضل الحلول الهندسية وإدارة المشاريع باحترافية عالية تضمن تسليمها في الوقت المحدد وضمن الميزانية المعتمدة.
              </p>
              
              <div className="space-y-4 mb-8">
                {[
                  "فريق هندسي متخصص ذو كفاءة عالية",
                  "استخدام أفضل المواد وأحدث تقنيات البناء",
                  "شفافية تامة في التسعير ومراحل العمل",
                  "الالتزام الصارم بالجدول الزمني للتسليم"
                ].map((item, i) => (
                  <div key={i} className="flex items-center gap-3">
                    <CheckCircle2 className="text-amber-500 w-6 h-6 shrink-0" />
                    <span className="text-slate-700 font-medium text-lg">{item}</span>
                  </div>
                ))}
              </div>
              
              <button className="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3.5 rounded font-bold transition-all shadow-md flex items-center gap-2">
                تعرف على فريقنا
                <ArrowLeft className="w-5 h-5" />
              </button>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Featured Projects */}
      <section id="projects" className="py-24 bg-slate-900 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
            <div className="max-w-2xl">
              <h2 className="text-amber-500 font-bold tracking-wider uppercase mb-2">معرض الأعمال</h2>
              <h3 className="text-3xl md:text-4xl font-bold mb-4">مشاريع نفخر بها</h3>
              <p className="text-slate-400 text-lg">جولة في أبرز أعمالنا التي تجسد التزامنا بالجودة والابتكار في مجال البناء والتشييد.</p>
            </div>
            <button className="shrink-0 bg-transparent hover:bg-white/10 text-white border border-white/20 px-6 py-3 rounded font-medium transition-all flex items-center gap-2 w-fit">
              عرض كل المشاريع
              <ChevronLeft className="w-4 h-4" />
            </button>
          </div>

          <div className="grid md:grid-cols-2 gap-8">
            <motion.div 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              className="group relative rounded-xl overflow-hidden cursor-pointer"
            >
              <div className="aspect-[4/3] w-full">
                <img src="/__mockup/images/contracting-project-1.png" alt="Project 1" className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
              </div>
              <div className="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent flex flex-col justify-end p-8">
                <div className="translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                  <span className="text-amber-400 text-sm font-bold tracking-wider uppercase mb-2 block">فيلا سكنية</span>
                  <h4 className="text-2xl font-bold text-white mb-2">مجمع فيلات الياسمين</h4>
                  <p className="text-slate-300 opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">تصميم وتنفيذ متكامل لفيلا عصرية بمواصفات ذكية.</p>
                </div>
              </div>
            </motion.div>

            <motion.div 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.2 }}
              className="group relative rounded-xl overflow-hidden cursor-pointer"
            >
              <div className="aspect-[4/3] w-full">
                <img src="/__mockup/images/contracting-project-2.png" alt="Project 2" className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
              </div>
              <div className="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent flex flex-col justify-end p-8">
                <div className="translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                  <span className="text-amber-400 text-sm font-bold tracking-wider uppercase mb-2 block">مشروع تجاري</span>
                  <h4 className="text-2xl font-bold text-white mb-2">برج الأعمال الإداري</h4>
                  <p className="text-slate-300 opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">تنفيذ مبنى مكاتب بواجهات زجاجية حديثة وتشطيبات فاخرة.</p>
                </div>
              </div>
            </motion.div>
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
                      <div className="text-lg font-medium font-sans" dir="ltr">info@arkan-build.com</div>
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
                
                <form className="space-y-6">
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
                    <label className="text-sm font-medium text-slate-700">تفاصيل المشروع</label>
                    <textarea rows={4} className="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all resize-none" placeholder="نبذة عن المساحة والموقع والطلبات الخاصة..."></textarea>
                  </div>
                  
                  <button type="button" className="w-full bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold py-4 rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-lg">
                    إرسال الطلب
                    <ArrowLeft className="w-5 h-5" />
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-slate-950 text-slate-400 py-12 border-t border-slate-900">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-4 gap-8 mb-12">
            <div className="col-span-2">
              <div className="flex items-center gap-2 mb-6">
                <div className="w-10 h-10 bg-amber-500 rounded flex items-center justify-center">
                  <Building2 className="text-slate-900 w-6 h-6" />
                </div>
                <span className="font-bold text-2xl tracking-tight text-white">أركان<span className="text-amber-500">البناء</span></span>
              </div>
              <p className="max-w-sm mb-6 leading-relaxed">
                شريكك الموثوق في عالم المقاولات والبناء. نقدم حلولاً هندسية متكاملة تجمع بين التصميم العصري وجودة التنفيذ.
              </p>
            </div>
            
            <div>
              <h4 className="text-white font-bold mb-6">روابط سريعة</h4>
              <ul className="space-y-3">
                <li><a href="#services" className="hover:text-amber-500 transition-colors">خدماتنا</a></li>
                <li><a href="#projects" className="hover:text-amber-500 transition-colors">مشاريعنا</a></li>
                <li><a href="#about" className="hover:text-amber-500 transition-colors">من نحن</a></li>
                <li><a href="#contact" className="hover:text-amber-500 transition-colors">اتصل بنا</a></li>
              </ul>
            </div>
            
            <div>
              <h4 className="text-white font-bold mb-6">الخدمات</h4>
              <ul className="space-y-3">
                <li><a href="#" className="hover:text-amber-500 transition-colors">المقاولات العامة</a></li>
                <li><a href="#" className="hover:text-amber-500 transition-colors">بناء الفيلات</a></li>
                <li><a href="#" className="hover:text-amber-500 transition-colors">المشاريع التجارية</a></li>
                <li><a href="#" className="hover:text-amber-500 transition-colors">أعمال التشطيب</a></li>
              </ul>
            </div>
          </div>
          
          <div className="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p>© 2025 شركة أركان البناء للمقاولات. جميع الحقوق محفوظة.</p>
            <div className="flex gap-4">
              <a href="#" className="w-10 h-10 bg-slate-900 rounded-full flex items-center justify-center hover:bg-amber-500 hover:text-slate-900 transition-all">
                <span className="sr-only">Twitter</span>
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                </svg>
              </a>
              <a href="#" className="w-10 h-10 bg-slate-900 rounded-full flex items-center justify-center hover:bg-amber-500 hover:text-slate-900 transition-all">
                <span className="sr-only">Instagram</span>
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path fillRule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clipRule="evenodd" />
                </svg>
              </a>
              <a href="#" className="w-10 h-10 bg-slate-900 rounded-full flex items-center justify-center hover:bg-amber-500 hover:text-slate-900 transition-all">
                <span className="sr-only">LinkedIn</span>
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path fillRule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clipRule="evenodd" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}
