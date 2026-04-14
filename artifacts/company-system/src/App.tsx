import { useEffect, type ReactNode } from "react";
import { Switch, Route, Router as WouterRouter } from "wouter";
import { useLocation } from "wouter";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { Toaster } from "@/components/ui/toaster";
import { TooltipProvider } from "@/components/ui/tooltip";
import NotFound from "@/pages/not-found";
import { Home } from "@/pages/home";
import { Login } from "@/pages/login";
import { EmployeeDashboard } from "@/pages/employee-dashboard";
import { ClientPortal } from "@/pages/client-portal";
import { AdminDashboard } from "@/pages/admin-dashboard";

const queryClient = new QueryClient();

function ProtectedRoute({ role, children }: { role: "admin" | "employee" | "client"; children: ReactNode }) {
  const [, setLocation] = useLocation();
  const currentRole = localStorage.getItem("userRole");

  useEffect(() => {
    if (currentRole !== role) {
      setLocation("/login");
    }
  }, [currentRole, role, setLocation]);

  if (currentRole !== role) {
    return null;
  }

  return children;
}

function AdminRoute() {
  return <ProtectedRoute role="admin"><AdminDashboard /></ProtectedRoute>;
}

function EmployeeRoute() {
  return <ProtectedRoute role="employee"><EmployeeDashboard /></ProtectedRoute>;
}

function ClientRoute() {
  return <ProtectedRoute role="client"><ClientPortal /></ProtectedRoute>;
}

function Router() {
  return (
    <Switch>
      <Route path="/" component={Home} />
      <Route path="/login" component={Login} />
      <Route path="/employee" component={EmployeeRoute} />
      <Route path="/client" component={ClientRoute} />
      <Route path="/admin" component={AdminRoute} />
      <Route component={NotFound} />
    </Switch>
  );
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <TooltipProvider>
        <WouterRouter base={import.meta.env.BASE_URL.replace(/\/$/, "")}>
          <Router />
        </WouterRouter>
        <Toaster />
      </TooltipProvider>
    </QueryClientProvider>
  );
}

export default App;
