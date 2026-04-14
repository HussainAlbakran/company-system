import { Router, type IRouter } from "express";
import healthRouter from "./health";
import companyRouter from "./company";

const router: IRouter = Router();

router.use(healthRouter);
router.use(companyRouter);

export default router;
